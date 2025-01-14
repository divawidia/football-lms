<div class="card">
    <div class="card-body">
        <x-table :headers="[
                'Pos.',
                'Name',
                'Team',
                'Apps',
                'Goals',
                'Assists',
                'Own Goals',
                'Shots',
                'Passes',
                'Fouls Conceded',
                'Yellow Cards',
                'Red Cards',
                'Saves',
                'Action'
            ]"
                 tableId="playersLeaderboardTable"
        />
    </div>
</div>

@push('addon-script')
    <script>
        $(document).ready(function (){
            $('#playersLeaderboardTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! $playersLeaderboardRoute !!}',
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'teams', name: 'teams' },
                    { data: 'apps', name: 'apps'},
                    { data: 'goals', name: 'goals'},
                    { data: 'assists', name: 'assists'},
                    { data: 'ownGoals', name: 'ownGoals'},
                    { data: 'shots', name: 'shots'},
                    { data: 'passes', name: 'passes'},
                    { data: 'fouls', name: 'fouls'},
                    { data: 'yellowCards', name: 'yellowCards'},
                    { data: 'redCards', name: 'redCards'},
                    { data: 'saves', name: 'saves'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ],
                order: [[4, 'desc']]
            });
        });
    </script>
@endpush
