<x-app-layout>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">User Monthly Analysis Report</h4>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('reports.user-monthly-analysis') }}" class="row align-items-end mb-3">
                            <div class="col-md-3">
                                <label for="start_date">From Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}" required>
                            </div>
                            <div class="col-md-3">
                                <label for="end_date">To Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}" required>
                            </div>
                            <div class="col-md-3">
                                <label for="user_id">User</label>
                                <select class="form-control select2" id="user_id" name="user_id">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ (string) $userId === (string) $user->id ? 'selected' : '' }}>
                                            {{ $user->employee_codes ? $user->employee_codes . ' - ' : '' }}{{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 d-flex">
                                <button type="submit" class="btn btn-theme mr-2">Filter</button>
                                <a class="btn btn-success" href="{{ route('reports.user-monthly-analysis.download', ['start_date' => $startDate, 'end_date' => $endDate, 'user_id' => $userId]) }}">
                                    <i class="material-icons">cloud_download</i> Export
                                </a>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm text-center no-wrap">
                                <thead class="text-primary">
                                    <tr>
                                        <th>Employee Code</th>
                                        <th>Employee Name</th>
                                        <th>Designation</th>
                                        <th>Total Mechanic MTD</th>
                                        <th>Total Retailer MTD</th>
                                        <th>Total Distributor MTD</th>
                                        <th>Working Days MTD</th>
                                        <th>Average Visits MTD</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($report['rows'] as $row)
                                        @php($totalVisits = array_sum($row['totals']))
                                        <tr>
                                            <td>{{ $row['employee_code'] }}</td>
                                            <td class="text-left">{{ $row['name'] }}</td>
                                            <td>{{ $row['designation'] }}</td>
                                            <td>{{ $row['totals']['mechanic'] }}</td>
                                            <td>{{ $row['totals']['retailer'] }}</td>
                                            <td>{{ $row['totals']['distributor'] }}</td>
                                            <td>{{ $row['working_days'] }}</td>
                                            <td>{{ $row['working_days'] ? round($totalVisits / $row['working_days'], 2) : 0 }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="8">No users found for this filter.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</x-app-layout>
