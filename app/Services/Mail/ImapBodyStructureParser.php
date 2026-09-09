<?php

namespace App\Services\Mail;

class ImapBodyStructureParser
{
    /**
     * @return array<int, array{
     *     part: string,
     *     content_type: string,
     *     encoding: string,
     *     size: int,
     *     disposition: string,
     *     filename: string
     * }>
     */
    public function parts(string $structure): array
    {
        $structure = trim($structure);

        if ($structure === '') {
            return [];
        }

        $tree = $this->parse($structure);
        $parts = [];

        $this->collectParts(
            $tree,
            '',
            $parts,
        );

        return $parts;
    }

    /**
     * @return array<int, array{
     *     part: string,
     *     filename: string,
     *     content_type: string,
     *     size: int,
     *     disposition: string,
     *     encoding: string
     * }>
     */
    public function attachments(string $structure): array
    {
        return array_values(
            array_filter(
                $this->parts($structure),
                static fn (array $part): bool =>
                    $part['disposition'] === 'attachment'
                    || $part['filename'] !== '',
            ),
        );
    }

    /**
     * @return array<int, mixed>
     */
    private function parse(string $structure): array
    {
        $tokens = $this->tokenize($structure);
        $index = 0;
        $values = [];

        while ($index < count($tokens)) {
            $values[] = $this->parseValue(
                $tokens,
                $index,
            );
        }

        if (
            count($values) === 1
            && is_array($values[0])
        ) {
            return $values[0];
        }

        return $values;
    }

    /**
     * @return array<int, array{
     *     type: string,
     *     value?: mixed
     * }>
     */
    private function tokenize(string $value): array
    {
        $tokens = [];
        $length = strlen($value);
        $index = 0;

        while ($index < $length) {
            $char = $value[$index];

            if (ctype_space($char)) {
                $index++;
                continue;
            }

            if ($char === '(') {
                $tokens[] = [
                    'type' => 'lparen',
                ];

                $index++;
                continue;
            }

            if ($char === ')') {
                $tokens[] = [
                    'type' => 'rparen',
                ];

                $index++;
                continue;
            }

            if ($char === '"') {
                $index++;
                $buffer = '';

                while ($index < $length) {
                    $current = $value[$index];

                    if ($current === '\\') {
                        $index++;

                        if ($index < $length) {
                            $buffer .= $value[$index];
                            $index++;
                        }

                        continue;
                    }

                    if ($current === '"') {
                        $index++;
                        break;
                    }

                    $buffer .= $current;
                    $index++;
                }

                $tokens[] = [
                    'type' => 'value',
                    'value' => $buffer,
                ];

                continue;
            }

            $start = $index;

            while (
                $index < $length
                && ! ctype_space($value[$index])
                && $value[$index] !== '('
                && $value[$index] !== ')'
            ) {
                $index++;
            }

            $atom = substr(
                $value,
                $start,
                $index - $start,
            );

            if (strcasecmp($atom, 'NIL') === 0) {
                $atomValue = null;
            } elseif (
                preg_match(
                    '/^-?[0-9]+$/',
                    $atom,
                )
            ) {
                $atomValue = (int) $atom;
            } else {
                $atomValue = $atom;
            }

            $tokens[] = [
                'type' => 'value',
                'value' => $atomValue,
            ];
        }

        return $tokens;
    }

    /**
     * @param array<int, array{
     *     type: string,
     *     value?: mixed
     * }> $tokens
     */
    private function parseValue(
        array $tokens,
        int &$index,
    ): mixed {
        $token = $tokens[$index] ?? null;

        if ($token === null) {
            return null;
        }

        if ($token['type'] === 'lparen') {
            $index++;
            $values = [];

            while (
                $index < count($tokens)
                && ($tokens[$index]['type'] ?? null) !== 'rparen'
            ) {
                $values[] = $this->parseValue(
                    $tokens,
                    $index,
                );
            }

            if (
                $index < count($tokens)
                && ($tokens[$index]['type'] ?? null) === 'rparen'
            ) {
                $index++;
            }

            return $values;
        }

        $index++;

        return $token['value'] ?? null;
    }

    /**
     * @param array<int, mixed> $node
     * @param array<int, array{
     *     part: string,
     *     content_type: string,
     *     encoding: string,
     *     size: int,
     *     disposition: string,
     *     filename: string
     * }> $parts
     */
    private function collectParts(
        array $node,
        string $prefix,
        array &$parts,
    ): void {
        if ($this->isMultipart($node)) {
            $childNumber = 1;

            foreach ($node as $child) {
                if (! is_array($child)) {
                    break;
                }

                $part = $prefix === ''
                    ? (string) $childNumber
                    : $prefix . '.' . $childNumber;

                $this->collectParts(
                    $child,
                    $part,
                    $parts,
                );

                $childNumber++;
            }

            return;
        }

        if ($prefix === '') {
            $prefix = '1';
        }

        $type = strtolower(
            (string) ($node[0] ?? ''),
        );

        $subtype = strtolower(
            (string) ($node[1] ?? ''),
        );

        if ($type === '' || $subtype === '') {
            return;
        }

        $parameters = $this->parameterListToMap(
            $node[2] ?? null,
        );

        [
            $disposition,
            $dispositionParameters,
        ] = $this->extractDisposition(
            $node,
        );

        $filename = $this->decodeParameterValue(
            $dispositionParameters['filename*']
            ?? $dispositionParameters['filename']
            ?? $parameters['name*']
            ?? $parameters['name']
            ?? '',
        );

        $parts[] = [
            'part' => $prefix,

            'content_type' =>
                $type . '/' . $subtype,

            'encoding' => strtolower(
                (string) ($node[5] ?? ''),
            ),

            'size' => (int) (
                $node[6] ?? 0
            ),

            'disposition' => $disposition,

            'filename' => $filename,
        ];
    }

    /**
     * @param array<int, mixed> $node
     */
    private function isMultipart(
        array $node,
    ): bool {
        return isset($node[0])
            && is_array($node[0]);
    }

    /**
     * @return array<string, string>
     */
    private function parameterListToMap(
        mixed $value,
    ): array {
        if (! is_array($value)) {
            return [];
        }

        $parameters = [];
        $count = count($value);

        for (
            $index = 0;
            $index + 1 < $count;
            $index += 2
        ) {
            $key = strtolower(
                trim(
                    (string) $value[$index],
                ),
            );

            if ($key === '') {
                continue;
            }

            $parameters[$key] =
                (string) $value[$index + 1];
        }

        return $parameters;
    }

    /**
     * @param array<int, mixed> $node
     * @return array{
     *     0: string,
     *     1: array<string, string>
     * }
     */
    private function extractDisposition(
        array $node,
    ): array {
        foreach ($node as $index => $value) {
            if (
                $index < 7
                || ! is_array($value)
            ) {
                continue;
            }

            $candidate = strtolower(
                (string) ($value[0] ?? ''),
            );

            if (! in_array(
                $candidate,
                [
                    'attachment',
                    'inline',
                ],
                true,
            )) {
                continue;
            }

            return [
                $candidate,

                $this->parameterListToMap(
                    $value[1] ?? null,
                ),
            ];
        }

        return [
            '',
            [],
        ];
    }

    private function decodeParameterValue(
        string $value,
    ): string {
        $value = trim($value);

        if ($value === '') {
            return '';
        }

        if (
            preg_match(
                "/^([^']*)'[^']*'(.*)$/s",
                $value,
                $matches,
            )
        ) {
            $charset = trim(
                $matches[1],
            );

            $decoded = rawurldecode(
                $matches[2],
            );

            if (
                $charset !== ''
                && strcasecmp(
                    $charset,
                    'utf-8',
                ) !== 0
            ) {
                $converted =
                    @mb_convert_encoding(
                        $decoded,
                        'UTF-8',
                        $charset,
                    );

                if (
                    is_string($converted)
                    && $converted !== ''
                ) {
                    return $converted;
                }
            }

            return $decoded;
        }

        if (str_contains(
            $value,
            '=?',
        )) {
            $decoded = @iconv_mime_decode(
                $value,
                ICONV_MIME_DECODE_CONTINUE_ON_ERROR,
                'UTF-8',
            );

            if (
                is_string($decoded)
                && $decoded !== ''
            ) {
                return $decoded;
            }
        }

        return $value;
    }
}
