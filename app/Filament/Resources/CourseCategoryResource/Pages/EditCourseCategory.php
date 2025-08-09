<?php

namespace App\Filament\Resources\CourseCategoryResource\Pages;

use App\Filament\Resources\CourseCategoryResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;

class EditCourseCategory extends EditRecord
{
    protected static string $resource = CourseCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_courses')
                ->label('View Courses')
                ->icon('heroicon-o-academic-cap')
                ->color('primary')
                ->url(fn () => '#') // This would typically link to a courses list filtered by this category
                ->visible(fn () => $this->record->courses()->count() > 0),

            DeleteAction::make(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Course category updated successfully!')
            ->body('The course category changes have been saved.');
    }

    public function getFormSchema(int | string $columns = 2): array
    {
        return $this->getResource()::getFormViewLayout(
            $this->getResource()::form($this->makeForm())->getSchema(),
            $this->getSidebarSchema()
        );
    }

    protected function getSidebarSchema(): array
    {
        return [
            Section::make('Category Information')
                ->schema([
                    Placeholder::make('courses_count')
                        ->label('Associated Courses')
                        ->content(fn () => $this->record->courses()->count() . ' courses'),

                    Placeholder::make('created_at')
                        ->label('Created At')
                        ->content(fn () => $this->record->created_at->format('M j, Y g:i A')),

                    Placeholder::make('updated_at')
                        ->label('Last Updated')
                        ->content(fn () => $this->record->updated_at->format('M j, Y g:i A')),
                ])
                ->columnSpan(1),
        ];
    }
}
