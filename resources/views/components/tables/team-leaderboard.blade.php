<div class="card">
    <div class="card-body">
        <x-table :headers="[
                'Pos.',
                'Teams',
                'Match Played',
                'Won',
                'Drawn',
                'Lost',
                'Goals',
                'Goals Conceded',
                'Clean Sheets',
                'Own Goal',
                'Action'
            ]"
            tableId="teamsLeaderboardTable"
        />
    </div>
</div>

@push('addon-script')
    <script>
        $(document).ready(function (){
            $('#teamsLeaderboardTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! $teamsLeaderboardRoute !!}',
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'match', name: 'match' },
                    { data: 'won', name: 'won'},
                    { data: 'drawn', name: 'drawn'},
                    { data: 'lost', name: 'lost'},
                    { data: 'goals', name: 'goals'},
                    { data: 'goalsConceded', name: 'goalsConceded'},
                    { data: 'cleanSheets', name: 'cleanSheets'},
                    { data: 'ownGoals', name: 'ownGoals'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '15%'
                    },
                ],
                order: [[3, 'desc']]
            });
        });
    </script>
@endpush
