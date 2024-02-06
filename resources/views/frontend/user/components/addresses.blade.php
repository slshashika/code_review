<div class="row">

    <div class="col-md-12">

        @include('frontend.user.components.add_new_address')
        <h5>Billing Addresses</h5><hr/>
        <div class="table-responsive table-cart">

            <table class="table table-bordered table-striped">
            <thead>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Company</th>
                <th>Active Status</th>
                <th>Actions</th>
            </thead>

            <tbody>
                @foreach ($customer->billingAddresses as $billingAddress)
                    <tr>
                        <td>{{$billingAddress->first_name}}</td>
                        <td>{{$billingAddress->last_name}}</td>
                        <td>{{$billingAddress->email}}</td>
                        <td>{{$billingAddress->phone}}</td>
                        <td>{{$billingAddress->company}}</td>
                        <td>
                            @if ($billingAddress->active_status == 1)
                                <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>

                            @include('frontend.user.components.edit_address_billing')
                            @if ($billingAddress->active_status == 0)
                            @include('frontend.user.components.change_active_status_billing')

                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</div>

<hr/>

<div class="row">

    <div class="col-md-12">
        <h5>Shipping Addresses</h5><hr/>
        <div class="table-responsive table-cart">

            <table class="table table-bordered table-striped">
            <thead>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Company</th>
                <th>Active Status</th>
                <th>Actions</th>
            </thead>

            <tbody>
                @foreach ($customer->shippingAddresses as $shippingAddress)
                    <tr>
                        <td>{{$shippingAddress->first_name}}</td>
                        <td>{{$shippingAddress->last_name}}</td>
                        <td>{{$shippingAddress->email}}</td>
                        <td>{{$shippingAddress->phone}}</td>
                        <td>{{$shippingAddress->company}}</td>
                        <td>
                            @if ($shippingAddress->active_status == 1)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </td>
                        <td>

                            @include('frontend.user.components.edit_address_shipping')
                            
                            @if ($shippingAddress->active_status == 0)
                                @include('frontend.user.components.change_active_status_shipping')
                            @endif
                            
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</div>
