<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dua App Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    @auth
        <div class="sidebar d-none d-md-block">
            <div class="px-4 mb-5">
                <h5 class="fw-bold text-dark"><i class="fas fa-moon me-2"></i> DuaApp</h5>
            </div>
            <nav class="nav flex-column">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-home me-3"></i> Dashboard
                </a>
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                    href="{{ route('admin.users.index') }}">
                    <i class="fas fa-users me-3"></i> App Users
                </a> <a class="nav-link  {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}"
                    href="{{ route('admin.posts.index') }}">
                    <i class="fas fa-envelope-open-text me-3"></i> Posts & Duas

                    <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}"
                        href="{{ route('admin.reports.index') }}">
                        <i class="fas fa-exclamation-triangle me-3"></i> Reports
                    </a>
                    <a class="nav-link mt-5 text-danger" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-3"></i> Logout
                    </a>
            </nav>
        </div>

        <div class="main-content">
            @yield('content')
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    @else
        <div class="d-flex align-items-center justify-content-center" style="min-height: 100vh;">
            @yield('content')
        </div>
    @endauth





    <div id="ajax-modal-container"></div>

    <script>


        // 3. The "Self-Cleaning" loadModal Function
        function loadModal(url) {
            let container = document.getElementById('ajax-modal-container');

            // --- STEP A: Force Cleanup of Old Modals ---
            // 1. If there is an existing modal in the container, dispose of it
            let existingModalEl = container.querySelector('.modal');
            if (existingModalEl) {
                let existingInstance = bootstrap.Modal.getInstance(existingModalEl);
                if (existingInstance) {
                    existingInstance.dispose(); // Kill the Bootstrap instance logic
                }
            }

            // 2. NUKE the Backdrops: Remove any stuck black overlays
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

            // 3. Unlock the Body: Allow scrolling again just in case
            document.body.classList.remove('modal-open');
            document.body.style = '';
            // -------------------------------------------

            // Show Loading Spinner
            Swal.fire({
                title: 'Loading...',
                html: 'Fetching data...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            // Fetch & Show New Modal
            fetch(url)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.text();
                })
                .then(html => {
                    Swal.close(); // Close spinner

                    container.innerHTML = html;

                    let modalElement = container.querySelector('.modal');
                    let bsModal = new bootstrap.Modal(modalElement, {
                        backdrop: 'static', // Prevents closing when clicking outside (optional, keeps it stable)
                        keyboard: false
                    });
                    bsModal.show();

                    // Pagination Logic (Same as before)
                    let paginationNav = modalElement.querySelector('.modal-body nav') || modalElement.querySelector('.pagination');
                    if (paginationNav) {
                        paginationNav.addEventListener('click', function (e) {
                            let link = e.target.closest('.page-link');
                            if (link && link.href) {
                                e.preventDefault();
                                bsModal.hide();
                                loadModal(link.href);
                            }
                        });
                    }

                    // Cleanup on Close
                    modalElement.addEventListener('hidden.bs.modal', function () {
                        container.innerHTML = '';
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Could not load content.', 'error');
                });
        }


        function deleteItem(deleteUrl, reloadUrl) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // A. Show Loading
                    Swal.fire({ title: 'Deleting...', didOpen: () => Swal.showLoading() });

                    // B. Send DELETE Request
                    fetch(deleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}', // Laravel security token
                            'Content-Type': 'application/json'
                        }
                    })
                        .then(response => {
                            if (!response.ok) throw new Error('Failed to delete');
                            return response.json();
                        })
                        .then(data => {
                            // C. Success Message
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });

                            // D. Reload the Modal Content (without closing it)
                            loadModal(reloadUrl);
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error', 'Could not delete item.', 'error');
                        });
                }
            });
        }
    </script>


</body>

</html>