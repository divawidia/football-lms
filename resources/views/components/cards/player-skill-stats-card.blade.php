@if($allSkills == null)
    <x-warning-alert text="This player has not added any skill stats yet"/>
@else
    <div class="card">
        <div class="card-Header d-flex align-items-center p-3">
            <h4 class="card-title">SKILLS</h4>
            <div class="card-subtitle text-50 ml-auto">
                @if($allSkills->updated_at)
                    Last updated at {{ convertToDatetime($allSkills->updated_at) }}
                @endif
            </div>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <div class="row align-items-center">
                    <div class="col-3">
                        <strong class="flex">Controlling</strong>
                    </div>
                    <div class="col-9">
                        <div class="flex" style="max-width: 100%">
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-primary"
                                     role="progressbar"
                                     style="width: {{ $allSkills->controlling }}%;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row align-items-center">
                    <div class="col-3">
                        <strong class="flex">Receiving</strong>
                    </div>
                    <div class="col-9">
                        <div class="flex" style="max-width: 100%">
                            <div class="progress"
                                 style="height: 8px;">
                                <div class="progress-bar bg-primary"
                                     role="progressbar"
                                     style="width: {{ $allSkills->recieving }}%;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row align-items-center">
                    <div class="col-3">
                        <strong class="flex">Dribbling</strong>
                    </div>
                    <div class="col-9">
                        <div class="flex" style="max-width: 100%">
                            <div class="progress"
                                 style="height: 8px;">
                                <div class="progress-bar bg-primary"
                                     role="progressbar"
                                     style="width: {{ $allSkills->dribbling }}%;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row align-items-center">
                    <div class="col-3">
                        <strong class="flex">Passing</strong>
                    </div>
                    <div class="col-9">
                        <div class="flex" style="max-width: 100%">
                            <div class="progress"
                                 style="height: 8px;">
                                <div class="progress-bar bg-primary"
                                     role="progressbar"
                                     style="width: {{ $allSkills->passing }}%;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row align-items-center">
                    <div class="col-3">
                        <strong class="flex">Shooting</strong>
                    </div>
                    <div class="col-9">
                        <div class="flex" style="max-width: 100%">
                            <div class="progress"
                                 style="height: 8px;">
                                <div class="progress-bar bg-primary"
                                     role="progressbar"
                                     style="width: {{ $allSkills->shooting }}%;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row align-items-center">
                    <div class="col-3">
                        <strong class="flex">Crossing</strong>
                    </div>
                    <div class="col-9">
                        <div class="flex" style="max-width: 100%">
                            <div class="progress"
                                 style="height: 8px;">
                                <div class="progress-bar bg-primary"
                                     role="progressbar"
                                     style="width: {{ $allSkills->crossing }}%;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row align-items-center">
                    <div class="col-3">
                        <strong class="flex">Turning</strong>
                    </div>
                    <div class="col-9">
                        <div class="flex" style="max-width: 100%">
                            <div class="progress"
                                 style="height: 8px;">
                                <div class="progress-bar bg-primary"
                                     role="progressbar"
                                     style="width: {{ $allSkills->turning }}%;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row align-items-center">
                    <div class="col-3">
                        <strong class="flex">Ball Handling</strong>
                    </div>
                    <div class="col-9">
                        <div class="flex" style="max-width: 100%">
                            <div class="progress"
                                 style="height: 8px;">
                                <div class="progress-bar bg-primary"
                                     role="progressbar"
                                     style="width: {{ $allSkills->ballHandling }}%;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row align-items-center">
                    <div class="col-3">
                        <strong class="flex">Power Kicking</strong>
                    </div>
                    <div class="col-9">
                        <div class="flex" style="max-width: 100%">
                            <div class="progress"
                                 style="height: 8px;">
                                <div class="progress-bar bg-primary"
                                     role="progressbar"
                                     style="width: {{ $allSkills->powerKicking }}%;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row align-items-center">
                    <div class="col-3">
                        <strong class="flex">Goal Keeping</strong>
                    </div>
                    <div class="col-9">
                        <div class="flex" style="max-width: 100%">
                            <div class="progress"
                                 style="height: 8px;">
                                <div class="progress-bar bg-primary"
                                     role="progressbar"
                                     style="width: {{ $allSkills->goalKeeping }}%;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row align-items-center">
                    <div class="col-3">
                        <strong class="flex">Offensive Play</strong>
                    </div>
                    <div class="col-9">
                        <div class="flex" style="max-width: 100%">
                            <div class="progress"
                                 style="height: 8px;">
                                <div class="progress-bar bg-primary"
                                     role="progressbar"
                                     style="width: {{ $allSkills->offensivePlay }}%;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li class="list-group-item">
                <div class="row align-items-center">
                    <div class="col-3">
                        <strong class="flex">Defensive Play</strong>
                    </div>
                    <div class="col-9">
                        <div class="flex" style="max-width: 100%">
                            <div class="progress"
                                 style="height: 8px;">
                                <div class="progress-bar bg-primary"
                                     role="progressbar"
                                     style="width: {{ $allSkills->defensivePlay }}%;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
@endif
