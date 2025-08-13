@extends('layouts.app')

@section('title', 'Manage Subscription Offers')

@section('contents')
    <div class="container-fluid mt-4">
        <form>
            <input type="hidden" name="deleted" value="false">
        </form>

        <h4 class="fw-bold mb-4">Promo List</h4>

        <div class="row align-items-center mb-3">
            <div class="col-md-4">
                <form method="GET" action="{{ route('offers.index') }}">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Search membership plan" 
                               value="{{ request('search') }}">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i>
                        </button>
                        @if(request('search'))
                            <a href="{{ route('offers.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="col-md-8 text-md-end mt-2 mt-md-0">
                <a href="{{ route('offers.archive.index') }}" class="btn btn-sm btn-secondary px-3 me-2">
                    <i class="fas fa-archive"></i> View Archived
                </a>
                <button class="btn btn-sm btn-success px-3" data-bs-toggle="modal" data-bs-target="#addOfferModal">
                    + Add Offer
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 180px;">Membership Plan</th>
                        <th style="min-width: 250px;">Description</th>
                        <th>Price</th>
                        <th>Duration (Days)</th>
                        <th>Promo?</th>
                        <th>Promo Period</th>
                        <th>Status</th>
                        <th style="width: 200px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($offers as $offer)
                        <tr>
                            <td>{{ $offer->name }}</td>
                            <td class="text-truncate" style="max-width: 200px;" title="{{ $offer->description }}">
                                {{ $offer->description }}
                            </td>
                            <td>₱{{ number_format($offer->price, 2) }}</td>
                            <td>{{ $offer->duration_days }}</td>
                            <td>{{ $offer->is_promo ? 'Yes' : 'No' }}</td>
                            <td>
                                @if($offer->is_promo)
                                    {{ $offer->promo_start_date }} - {{ $offer->promo_end_date }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ ucfirst($offer->status) }}</td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <!-- Edit Button -->
                                    <button class="btn btn-sm btn-warning me-4"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editOfferModal"
                                            onclick="editOffer({{ $offer->id }}, '{{ $offer->name }}', '{{ $offer->description }}', {{ $offer->price }}, {{ $offer->duration_days }}, {{ $offer->is_promo ? 'true' : 'false' }}, '{{ $offer->promo_start_date }}', '{{ $offer->promo_end_date }}', '{{ $offer->status }}')">
                                        Edit
                                    </button>
                                    
                                    <!-- Archive Button -->
                                    <form action="{{ route('offers.archive', $offer->id) }}"
                                          method="POST" style="display:inline; margin-left: 20px;">
                                        @csrf
                                        <button class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to archive this membership plan?')">
                                            Archive
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-clipboard-list fa-2x mb-3"></i>
                                    <p class="mb-0">No active membership plans found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        

        <!-- Add Offer Modal -->
        <div class="modal fade" id="addOfferModal" tabindex="-1" aria-labelledby="addOfferModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('offers.store') }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Offer</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Membership Plan</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Description</label>
                                <textarea name="description" class="form-control" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label>Price</label>
                                <input type="number" step="0.01" name="price" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Duration (Days)</label>
                                <input type="number" name="duration_days" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Promo</label>
                                <select name="is_promo" class="form-control" id="is_promo_select" required>
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                            <div id="promo_dates_section" style="display:none;">
                                <div class="mb-3">
                                    <label>Promo Start Date</label>
                                    <input type="date" name="promo_start_date" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label>Promo End Date</label>
                                    <input type="date" name="promo_end_date" class="form-control">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="archived">Archived</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Add Offer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Offer Modal -->
        <div class="modal fade" id="editOfferModal" tabindex="-1" aria-labelledby="editOfferModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" id="editOfferForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Membership Plan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Membership Plan</label>
                                <input type="text" name="name" id="edit_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Description</label>
                                <textarea name="description" id="edit_description" class="form-control"></textarea>
                            </div>
                            <div class="mb-3">
                                <label>Price</label>
                                <input type="number" step="0.01" name="price" id="edit_price" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Duration (Days)</label>
                                <input type="number" name="duration_days" id="edit_duration_days" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Promo</label>
                                <select name="is_promo" class="form-control" id="edit_is_promo_select" required>
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                            <div id="edit_promo_dates_section" style="display:none;">
                                <div class="mb-3">
                                    <label>Promo Start Date</label>
                                    <input type="date" name="promo_start_date" id="edit_promo_start_date" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label>Promo End Date</label>
                                    <input type="date" name="promo_end_date" id="edit_promo_end_date" class="form-control">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label>Status</label>
                                <select name="status" id="edit_status" class="form-control" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="archived">Archived</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-warning">Update Plan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Add offer modal promo toggle
        document.getElementById('is_promo_select').addEventListener('change', function() {
            document.getElementById('promo_dates_section').style.display = this.value == '1' ? 'block' : 'none';
        });

        // Edit offer modal promo toggle
        document.getElementById('edit_is_promo_select').addEventListener('change', function() {
            document.getElementById('edit_promo_dates_section').style.display = this.value == '1' ? 'block' : 'none';
        });

        // Function to populate edit modal
        function editOffer(id, name, description, price, durationDays, isPromo, promoStartDate, promoEndDate, status) {
            // Set form action
            document.getElementById('editOfferForm').action = '/subscription-offers/' + id;
            
            // Populate form fields
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_duration_days').value = durationDays;
            document.getElementById('edit_is_promo_select').value = isPromo ? '1' : '0';
            document.getElementById('edit_promo_start_date').value = promoStartDate;
            document.getElementById('edit_promo_end_date').value = promoEndDate;
            document.getElementById('edit_status').value = status;
            
            // Show/hide promo dates section
            document.getElementById('edit_promo_dates_section').style.display = isPromo ? 'block' : 'none';
        }
    </script>
@endsection
