 <!-- Default Modals -->
 <div id="myModalEdit{{ $item['id'] }}" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
     style="display: none;">
     <div class="modal-dialog">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="myModalLabel">Modification </h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                 </button>
             </div>
             <div class="modal-body">
                 <form autocomplete="off" class="needs-validation" novalidate method="POST"
                     action="{{ route('admin-register.update', $item['id']) }}" enctype="multipart/form-data">
                     @csrf
                     <div class="mb-3">
                         <label for="username" class="form-label">Nom utilisateur</label>
                         <input type="text" value="{{ $item['username'] }}" name="username" class="form-control"
                             id="username" required>
                     </div>

                     <div class="mb-3">
                         <label for="username" class="form-label">Email</label>
                         <input type="email" value="{{ $item['email'] }}" name="email" class="form-control"
                             id="username" required>
                     </div>

                     <div class="mb-3">
                         <label for="username" class="form-label">Telephone</label>
                         <input type="number" value="{{ $item['phone'] }}" name="phone" class="form-control"
                             id="username" >
                     </div>

                     <div class="mb-3">
                         <label for="username" class="form-label">Mot de passe
                            <small class="text-danger">Nouveau mot de passe si vous voulez changer l'ancien</small>
                         </label>
                         <input type="password" name="password" class="form-control" id="username" autocomplete="off">
                     </div>

                     <div class="mb-3">
                         <label for="username" class="form-label">Role</label>
                         <select class="form-control" name="role" id="" required>
                             <option disabled selected value>Selectionner...</option>
                             @foreach ($data_role as $role)
                                 <option value="{{ $role['name'] }}"
                                     {{ $item['roles'][0]['name'] == $role['name'] ? 'selected' : '' }}>
                                     {{ $role['name'] }}
                                 </option>
                             @endforeach
                         </select>
                     </div>

                     <div class="mt-3">
                         <button class="btn btn-success w-100" type="submit">Valider</button>
                     </div>


                 </form>

             </div><!-- /.modal-content -->
         </div><!-- /.modal-dialog -->
     </div><!-- /.modal -->
 </div><!-- end col -->

 {{-- @section('script')
    <script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
    <script src="https://cdn.lordicon.com/libs/mssddfmo/lord-icon-2.1.0.js"></script>
    <script src="{{ URL::asset('build/js/pages/modal.init.js') }}"></script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>
@endsection --}}
