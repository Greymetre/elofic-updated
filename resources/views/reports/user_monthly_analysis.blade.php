@extends('layouts.app')

@section('content')
<div class="content">
    <div class="container-fluid">
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

                        <p class="text-muted"><span class="text-danger font-weight-bold">L</span> = Leave, <span class="text-danger font-weight-bold">A</span> = No punch-in, M/R/D = Mechanic/Retailer/Distributor visits.</p>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm text-center no-wrap">
                                <thead class="text-primary">
                                    <tr>
                                        <th>Employee</th>
                                        <th>Employee Name</th>
                                        <th>Designation</th>
                                        @foreach($report['dates'] as $date)
                                            <th>{{ date('d-M', strtotime($date)) }}</th>
                                        @endforeach
                                        <th>Total M/R/D</th>
                                        <th>Working Days</th>
                                        <th>Avg. M/R/D</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($report['rows'] as $row)
                                        <tr>
                                            <td>{{ $row['employee_code'] }}</td>
                                            <td class="text-left">{{ $row['name'] }}</td>
                                            <td>{{ $row['designation'] }}</td>
                                            @foreach($report['dates'] as $date)
                                                @php($day = $row['days'][$date])
                                                <td>
                                                    @if($day['status'] !== 'P')
                                                        <span class="text-danger font-weight-bold">{{ $day['status'] }}</span>
                                                    @else
                                                        {{ $day['mechanic'] }}/{{ $day['retailer'] }}/{{ $day['distributor'] }}
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td>{{ $row['totals']['mechanic'] }}/{{ $row['totals']['retailer'] }}/{{ $row['totals']['distributor'] }}</td>
                                            <td>{{ $row['working_days'] }}</td>
                                            <td>{{ $row['averages']['mechanic'] }}/{{ $row['averages']['retailer'] }}/{{ $row['averages']['distributor'] }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="{{ count($report['dates']) + 6 }}">No users found for this filter.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
