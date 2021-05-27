<?php

namespace Ouzo\Db;

class OnConflict
{
    const DO_NOTHING = 'DO_NOTHING';
    const UPDATE = 'UPDATE';
    private ?string $onConflictAction;
    private array $onConflictColumns;
    private array $onConflictUpdateValues;

    public function __construct(
        ?string $onConflictAction,
        array $onConflictColumns = [],
        array $onConflictUpdateValues = []
    )
    {
        $this->onConflictAction = $onConflictAction;
        $this->onConflictColumns = $onConflictColumns;
        $this->onConflictUpdateValues = $onConflictUpdateValues;
    }

    public function getOnConflictAction(): ?string
    {
        return $this->onConflictAction;
    }

    public function isDoNothingAction(): bool
    {
        return $this->onConflictAction === OnConflict::DO_NOTHING;
    }

    public function isUpdateAction(): bool
    {
        return $this->onConflictAction === OnConflict::UPDATE;
    }

    public function getOnConflictColumns(): array
    {
        return $this->onConflictColumns;
    }

    public function getOnConflictUpdateValues(): array
    {
        return $this->onConflictUpdateValues;
    }

    public static function doNothing(): OnConflict
    {
        return new OnConflict(OnConflict::DO_NOTHING);
    }

    public static function doUpdate(array $onConflictColumns, array $onConflictUpdateValues): OnConflict
    {
        return new OnConflict(OnConflict::UPDATE, $onConflictColumns, $onConflictUpdateValues);
    }
}