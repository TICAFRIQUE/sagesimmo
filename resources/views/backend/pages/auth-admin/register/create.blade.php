<div class="row">
    <div class="col-xxl-6">
        <div class="card">
            <!-- Default Modals -->
            <div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
                style="display: none;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="myModalLabel">Créer un nouveau administrateur </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <div class="modal-body">

                            <form class="needs-validation" novalidate method="POST"
                                action="{{ route('admin-register.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="username" class="form-label">Nom utilisateur</label>
                                    <input type="text" name="username" class="form-control" id="username" required>
                                </div>

                             
                                <div class="mb-3">
                                    <label for="username" class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" id="username"
                                        required>
                                </div>

                                 <div class="mb-3">
                                    <label for="username" class="form-label">Telephone</label>
                                    <input type="number" name="phone" class="form-control" id="username"
                                        >
                                </div>

                                 <div class="mb-3">
                                    <label for="password" class="form-label">Mot de passe</label>
                                    <div class="position-relative">
                                        <input type="password" name="password" class="form-control pe-5" id="password"
                                            required placeholder="Entrez votre mot de passe">
                                        <button type="button" class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" 
                                                style="padding: 0.5rem 0.75rem; height: 100%; border: none; background: none;"
                                                id="password-toggle"
                                                aria-label="Afficher/cacher le mot de passe">
                                            <i class="ri-eye-off-line" id="password-icon"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback">
                                        Veuillez saisir un mot de passe valide
                                    </div>
                                </div>

                                 <div class="mb-3">
                                    <label for="username" class="form-label">Role</label>
                                  <select class="form-control" name="role" id="" required>
                                    <option disabled selected value>Selectionner...</option>
                                    @foreach ($data_role as $item)
                                        <option value="{{$item['name']}}">{{$item['name']}} </option>
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
        </div><!-- end row -->
    </div><!-- end col -->
</div>
<!--end row-->

<style>
    .password-addon {
        cursor: pointer;
        transition: color 0.3s ease;
    }
    
    .password-addon:hover {
        color: #495057 !important;
    }
    
    .password-addon:focus {
        box-shadow: none;
        outline: none;
    }
    
    .password-addon i {
        font-size: 16px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordToggle = document.getElementById('password-toggle');
        const passwordInput = document.getElementById('password');
        const passwordIcon = document.getElementById('password-icon');
        
        if (passwordToggle && passwordInput && passwordIcon) {
            passwordToggle.addEventListener('click', function() {
                // Toggle password visibility
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    passwordIcon.className = 'ri-eye-line';
                    passwordToggle.setAttribute('aria-label', 'Cacher le mot de passe');
                } else {
                    passwordInput.type = 'password';
                    passwordIcon.className = 'ri-eye-off-line';
                    passwordToggle.setAttribute('aria-label', 'Afficher le mot de passe');
                }
                
                // Maintenir le focus sur l'input après le clic
                passwordInput.focus();
            });
            
            // Empêcher la soumission du formulaire lors du clic sur le bouton toggle
            passwordToggle.addEventListener('click', function(e) {
                e.preventDefault();
            });
        }
    });
</script>


