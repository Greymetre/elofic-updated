<x-app-layout>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-icon card-header-theme">
                    <div class="card-icon"><i class="material-icons">notifications_active</i></div>
                    <h4 class="card-title">Notification Management</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="{{ route('notification-management.send') }}" id="notificationForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <label for="state_id">State</label>
                                <select class="form-control select2 notification-filter" name="state_id" id="state_id">
                                    <option value="">All States</option>
                                    @foreach($states as $state)
                                        <option value="{{ $state->id }}">{{ $state->state_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="district_id">District</label>
                                <select class="form-control select2 notification-filter" name="district_id" id="district_id">
                                    <option value="">All Districts</option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district->id }}">{{ $district->district_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="city_id">City</label>
                                <select class="form-control select2 notification-filter" name="city_id" id="city_id">
                                    <option value="">All Cities</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}">{{ $city->city_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="user_id">User</label>
                                <select class="form-control select2 notification-filter" name="user_id" id="user_id">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}{{ $user->mobile ? ' - '.$user->mobile : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    Users with a valid FCM token: <strong id="recipientCount">{{ $recipientCount }}</strong>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="message">Notification Message</label>
                                <textarea class="form-control" id="message" name="message" rows="5" maxlength="1000"
                                    placeholder="Enter the message to send..." required>{{ old('message') }}</textarea>
                                <small class="text-muted">Maximum 1000 characters.</small>
                            </div>
                            <div class="col-md-12 mt-3">
                                <button type="submit" class="btn btn-theme" id="sendButton" {{ $recipientCount === 0 ? 'disabled' : '' }}>
                                    <i class="material-icons">send</i> Send Notification
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const filterUrl = @json(route('notification-management.filters'));
            const state = $('#state_id');
            const district = $('#district_id');
            const city = $('#city_id');
            const user = $('#user_id');
            let loading = false;

            function replaceOptions(element, rows, placeholder, labelKey, selectedValue) {
                element.empty().append(new Option(placeholder, ''));
                rows.forEach(function (row) {
                    element.append(new Option(row[labelKey], row.id, false, String(row.id) === String(selectedValue)));
                });
                element.trigger('change.select2');
            }

            function refreshFilters(changedId) {
                if (loading) return;
                loading = true;

                if (changedId === 'state_id') {
                    district.val('');
                    city.val('');
                    user.val('');
                } else if (changedId === 'district_id') {
                    city.val('');
                    user.val('');
                } else if (changedId === 'city_id') {
                    user.val('');
                }

                const values = {
                    state_id: state.val(),
                    district_id: district.val(),
                    city_id: city.val(),
                    user_id: user.val()
                };

                $.get(filterUrl, values).done(function (response) {
                    replaceOptions(district, response.districts, 'All Districts', 'district_name', values.district_id);
                    replaceOptions(city, response.cities, 'All Cities', 'city_name', values.city_id);
                    replaceOptions(user, response.users, 'All Users', 'display_name', values.user_id);
                    $('#recipientCount').text(response.recipient_count);
                    $('#sendButton').prop('disabled', response.recipient_count === 0);
                }).always(function () {
                    loading = false;
                });
            }

            $('.notification-filter').on('change', function () {
                refreshFilters(this.id);
            });

            $('#notificationForm').on('submit', function () {
                $('#sendButton').prop('disabled', true).text('Sending...');
            });
        });
    </script>
</x-app-layout>
