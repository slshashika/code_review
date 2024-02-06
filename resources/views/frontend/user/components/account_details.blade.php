             <form action="{{route('web.user.profileUpdate')}}" method="post" id="account-form">
                {{csrf_field()}}
                <div class="row">
                   <div class="form-group col-xl-6 col-12">
                      <label for="" class="pb-2 fw-bolder summary-p">First Name</label>
                      <input type="text" class="form-control input-check" placeholder="First Name" name="first_name" value="{{$customer->first_name}}">
                      <input type="text" hidden name="customer_id" value="{{$customer->id}}">
                   </div>
                   <div class="form-group col-xl-6 col-12">
                      <label for="" class="pb-2 fw-bolder summary-p">Last Name</label>
                      <input type="text" class="form-control input-check" name="last_name" value="{{$customer->last_name}}" placeholder="Last Name">
                   </div>
                </div>

                <div class="row">
                   <div class="form-group col-xl-6 col-12">
                      <label for="" class="pb-2 fw-bolder summary-p">Email</label>
                      <input type="email" class="form-control input-check" placeholder="Email" name="email" value="{{$customer->email}}">
                   </div>
                   <div class="form-group col-xl-6 col-12">
                      <label for="" class="pb-2 fw-bolder summary-p">Phone</label>
                      <input type="tel" class="form-control input-check" onkeypress="validatePhoneNumber()" oninput="this.value = this.value.replace(/[^0-9.+]/g, '').replace(/(\..*)\./g, '$1');" name="phone" id="customer-phone" value="{{$customer->phone}}" placeholder="Phone">
                      <span class="text-white" id="customer-phone-error-message"></span>
                     </div>
                </div>
                
               <fieldset class="scheduler-border my-4 pt-1">

                   <legend class="scheduler-border fw-normal profile-p">Password change</legend>

                   <div class="form-group col-12 pt-1">
                      <label for="" class="pb-2 fw-bolder summary-p">Current password (leave blank to leave unchanged)
                      </label>
                      <input type="password" class="form-control input-check" placeholder="Current password" name="current_password" value="">
                  </div>

                   <div class="form-group col-12 pt-1">
                      <label for="" class="pb-2 fw-bolder summary-p">New password (leave blank to leave unchanged)</label>
                      <input type="password" class="form-control input-check" placeholder="New password" name="new_password" value="">
                   </div>

                   <div class="form-group col-12 pt-1">
                      <label for="" class="pb-2 fw-bolder summary-p">Confirm new password</label>
                      <input type="password" class="form-control input-check" placeholder="Confirm new password" name="confirm_password" value="">
                   </div>

               </fieldset>

                 <div class="row">
                   <div class="form-group col-12">
                      <button class="btn btn-primary" >Save changes</button>
                  </div>
                 </div>

             </form>


