<!-- Button trigger modal -->
<button type="button" style="float:right;" class="btn btn-dark btn-sm text-white" data-bs-toggle="modal" data-bs-target="#new-address">
 <i class="fa fa-plus"></i> New Address
</button>

<!-- Modal -->
<div class="modal fade" id="new-address" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="max-width:900px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">New Address</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <!-- Form Start Here -->
      <form action="{{route('web.user.addNewAddress')}}" method="post">
          {{csrf_field()}}
         <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">Address Typ</label>
                        <select name="address_type" class="form-control">
                            <option value="0">Billing</option>
                            <option value="1">Shipping</option>
                        </select>
                        <input type="text" hidden name="customer_id" value="{{$customer->id}}">
                    </div>
                    <div class="mb-3">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="phone-no" name="phone" onChange="removeCountryCode('phone-no')" oninput="this.value = this.value.replace(/[^0-9.+]/g, '').replace(/(\..*)\./g, '$1');" required>
                        
                    </div>
                    <div class="mb-3">
                        <label for="company" class="form-label">Company</label>
                        <input type="text" class="form-control" id="company" name="company" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="address_line1" class="form-label">Address Line 1</label>
                        <input type="text" class="form-control" id="address_line1" name="address_line1" required>
                    </div>
                    <div class="mb-3">
                        <label for="address_line2" class="form-label">Address Line 2</label>
                        <input type="text" class="form-control" id="address_line2" name="address_line2" >
                    </div>                   
                    <div class="mb-3">
                        <label for="state" class="form-label">State</label>
                        <input type="text" class="form-control" id="state" name="state" required>
                    </div>
                    <div class="mb-3">
                        <label for="city" class="form-label">City</label>
                        <select name="city" style="z-index:1000;" id="newAddressCity" class="form-control js-example-basic-single" required onChange="getZipCodeForCity('newAddressCity','newAddressPostalCode')">
                            @foreach ($zones as $zone)
                                <option value="{{$zone->zone_name}}">{{$zone->zone_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="zip" class="form-label">Postal Code</label>
                        <input type="text" class="form-control" id="newAddressPostalCode" name="zip" readonly required>
                            <select name="zip" id="zip" class="form-control js-example-basic-single" required>
                                @foreach ($zones as $zone)
                                <option value="{{$zone->zip_code}}">{{$zone->zip_code}}</option>
                                @endforeach
                             </select>
                    </div>
                    <div class="mb-3">
                        <label for="country" class="form-label">Country</label>
                        <select class="form-control" id="country" name="country" required>
                            @foreach ($countries as $country)
                                <option value="{{$country->country_name}}">{{$country->country_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
      </div>
      <div class="modal-footer">          
        <button type="submit" class="btn btn-primary text-white">Add Address</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </form>
    <!-- End form -->
    </div>
  </div>
</div>