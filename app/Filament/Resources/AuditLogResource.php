<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLog\Pages\ListAuditLogs;
use App\Models\AuditLog;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\HtmlString;
use UnitEnum;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Audit Log';
    protected static ?string $modelLabel = 'log entry';
    protected static ?string $pluralModelLabel = 'audit logs';
    protected static string|UnitEnum|null $navigationGroup = 'System';
    protected static ?int $navigationSort = 4;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(EloquentModel $record): bool
    {
        return false;
    }

    public static function canEdit(EloquentModel $record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('timestamp', 'desc')
            ->striped()
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateHeading('No events recorded')
            ->paginated([25, 50, 100])
            ->columns([
                TextColumn::make('timestamp')
                    ->label('Date/Time')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),
                TextColumn::make('user.email')
                    ->label('User')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('action')
                    ->label('Action')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'created' => 'success',
                        'updated' => 'info',
                        'deleted' => 'danger',
                        'approved' => 'success',
                        'rejected' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->sortable(),
                TextColumn::make('model_id')
                    ->label('Record ID')
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('model_type')
                    ->label('Model')
                    ->formatStateUsing(fn (string $state) => class_basename($state))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->label('IP')
                    ->placeholder('—')
                    ->copyable(),
                TextColumn::make('user_agent')
                    ->label('User Agent')
                    ->placeholder('—')
                    ->limit(40)
                    ->tooltip(fn (AuditLog $r) => $r->user_agent),
            ])
            ->recordActions([
                Action::make('viewDiff')
                    ->label('Details')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading(fn (AuditLog $r) => ucfirst($r->action) . ' — ' . class_basename($r->model_type) . ' #' . $r->model_id)
                    ->modalContent(fn (AuditLog $r) => new HtmlString(
                        '<div class="space-y-4 font-mono text-sm">'
                        . static::renderDiffBlock('Before', $r->old_values, 'bg-red-50 dark:bg-red-950 border-red-200 dark:border-red-800')
                        . static::renderDiffBlock('After', $r->new_values, 'bg-green-50 dark:bg-green-950 border-green-200 dark:border-green-800')
                        . '</div>'
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])
            ->filters([
                SelectFilter::make('action')
                    ->label('Action')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                SelectFilter::make('model_type')
                    ->label('Model')
                    ->options(fn () => AuditLog::query()
                        ->distinct('model_type')
                        ->pluck('model_type', 'model_type')
                        ->mapWithKeys(fn ($v) => [$v => class_basename($v)])
                        ->toArray()),
                Filter::make('until')
                    ->schema([\Filament\Forms\Components\DatePicker::make('until')->label('Up to date')])
                    ->query(fn (Builder $q, array $data) => $q->when($data['until'] ?? null, fn ($q, $d) => $q->where('timestamp', '<=', Carbon::parse($d)->endOfDay()))),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuditLogs::route('/'),
        ];
    }

    private static function renderDiffBlock(string $label, ?array $values, string $classes): string
    {
        if (empty($values)) {
            return '';
        }
        $json = json_encode($values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $escaped = htmlspecialchars($json, ENT_QUOTES, 'UTF-8');
        return '<div class="rounded border ' . $classes . ' p-3">'
            . '<div class="mb-1 font-semibold text-xs uppercase tracking-wide opacity-60">' . $label . '</div>'
            . '<pre class="whitespace-pre-wrap break-all text-xs">' . $escaped . '</pre>'
            . '</div>';
    }
}
