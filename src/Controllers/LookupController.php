<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\ProspectStatusModel;
use App\Models\SourceModel;
use App\Models\TagModel;

final class LookupController
{
    private ProspectStatusModel $statuses;
    private SourceModel $sources;
    private TagModel $tags;

    public function __construct()
    {
        $this->statuses = new ProspectStatusModel();
        $this->sources = new SourceModel();
        $this->tags = new TagModel();
    }

    public function statuses(Request $request): void
    {
        unset($request);
        Response::json(['data' => $this->statuses->all()]);
    }

    public function sources(Request $request): void
    {
        unset($request);
        Response::json(['data' => $this->sources->all()]);
    }

    public function tags(Request $request): void
    {
        unset($request);
        Response::json(['data' => $this->tags->all()]);
    }
}
