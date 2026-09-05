<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ContactInquiries\ContactInquiryResource;
use App\Models\ContactInquiry;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentInquiries extends TableWidget
{
    protected static ?string $heading = 'Recent Inquiries';

    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 3,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ContactInquiry::query()
                    ->latest('created_at')
                    ->limit(6)
            )

            ->columns([

                TextColumn::make('name')
                    ->label('Contact')
                    ->searchable()
                    ->weight('medium')
                    ->description(
                        fn (ContactInquiry $record): ?string =>
                        $record->company ?: $record->email
                    ),

                TextColumn::make('project_type')
                    ->label('Project Type')
                    ->placeholder('—')
                    ->limit(35),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(
                        fn (?string $state): string =>
                        match ($state) {
                            'new' => 'New',
                            'reviewed' => 'Reviewed',
                            'contacted' => 'Contacted',
                            'closed' => 'Closed',
                            default => ucfirst((string) $state),
                        }
                    )
                    ->color(
                        fn (?string $state): string =>
                        match ($state) {
                            'new' => 'warning',
                            'reviewed' => 'info',
                            'contacted' => 'success',
                            'closed' => 'gray',
                            default => 'gray',
                        }
                    ),

                TextColumn::make('created_at')
                    ->label('Received')
                    ->since()
                    ->dateTimeTooltip('M j, Y — H:i')
                    ->sortable(),

            ])

            ->recordActions([
                Action::make('view')
                    ->label('Open')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(
                        fn (): string =>
                        ContactInquiryResource::getUrl('index')
                    ),
            ])

            ->emptyStateHeading('No inquiries yet')
            ->emptyStateDescription(
                'New contact inquiries will appear here.'
            )
            ->emptyStateIcon('heroicon-o-envelope')

            ->paginated(false)
            ->striped();
    }
}
