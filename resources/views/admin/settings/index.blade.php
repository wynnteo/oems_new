@extends('layouts.master')

@section('title')
Settings | Admin Panel
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header actions">
                    <h5 class="text-capitalize"><i class="material-icons opacity-10">settings</i>  System Settings</h5>
                    <div class="actions_item">
                        <button type="button" class="btn btn-info me-2" onclick="testEmail()" title="Test Email">
                            <i class="material-icons">email</i> Test Email
                        </button>
                        <button type="button" class="btn btn-warning me-2" onclick="clearCache()" title="Clear Cache">
                            <i class="material-icons">cached</i> Clear Cache
                        </button>
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                <i class="material-icons">more_vert</i> More Actions
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="exportSettings()">
                                    <i class="material-icons">download</i> Export Settings
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="$('#import-modal').modal('show')">
                                    <i class="material-icons">upload</i> Import Settings
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="#" onclick="resetSettings()">
                                    <i class="material-icons">restore</i> Reset to Defaults
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body pb-2">
                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible text-white">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if (session('success'))
                    <div class="alert alert-success alert-dismissible text-white">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        {{ session('success') }}
                    </div>
                    @endif

                    <!-- Settings Tabs -->
                    <div class="row">
                        <div class="col-12">
                            <div class="nav-wrapper position-relative">
                                <ul class="nav nav-pills nav-fill p-1 bg-transparent" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link mb-0 px-0 py-1 active" data-bs-toggle="tab" 
                                           href="#general-tab" role="tab">
                                            <i class="material-icons text-lg">settings</i>
                                            <span class="ms-1">General</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" 
                                           href="#email-tab" role="tab">
                                            <i class="material-icons text-lg">email</i>
                                            <span class="ms-1">Email</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" 
                                           href="#certificate-tab" role="tab">
                                            <i class="material-icons text-lg">card_membership</i>
                                            <span class="ms-1">Certificate</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" 
                                           href="#notification-tab" role="tab">
                                            <i class="material-icons text-lg">notifications</i>
                                            <span class="ms-1">Notifications</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link mb-0 px-0 py-1" data-bs-toggle="tab" 
                                           href="#system-tab" role="tab">
                                            <i class="material-icons text-lg">computer</i>
                                            <span class="ms-1">System</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Settings Form -->
                    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" id="settings-form">
                        @csrf
                        
                        <div class="tab-content" id="settingsTabContent">
                            <!-- General Settings Tab -->
                            <div class="tab-pane fade show active" id="general-tab" role="tabpanel">
                                <div class="row mt-4">
                                    @if(isset($settingsGroups['general']))
                                        @foreach($settingsGroups['general'] as $setting)
                                            <div class="col-xs-12 col-sm-12 col-md-6">
                                                <div class="form-group">
                                                    <strong>{{ $setting->label }}:</strong>
                                                    @if($setting->description)
                                                        <small class="d-block text-muted mb-2">{{ $setting->description }}</small>
                                                    @endif
                                                    
                                                    @include('admin.settings.partials.field', ['setting' => $setting])
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <!-- Email Settings Tab -->
                            <div class="tab-pane fade" id="email-tab" role="tabpanel">
                                <div class="row mt-4">
                                    @if(isset($settingsGroups['email']))
                                        @foreach($settingsGroups['email'] as $setting)
                                            <div class="col-xs-12 col-sm-12 col-md-6">
                                                <div class="form-group">
                                                    <strong>{{ $setting->label }}:</strong>
                                                    @if($setting->description)
                                                        <small class="d-block text-muted mb-2">{{ $setting->description }}</small>
                                                    @endif
                                                    
                                                    @include('admin.settings.partials.field', ['setting' => $setting])
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <!-- Certificate Settings Tab -->
                            <div class="tab-pane fade" id="certificate-tab" role="tabpanel">
                                <div class="row mt-4">
                                    @if(isset($settingsGroups['certificate']))
                                        @foreach($settingsGroups['certificate'] as $setting)
                                            <div class="col-xs-12 col-sm-12 col-md-6">
                                                <div class="form-group">
                                                    <strong>{{ $setting->label }}:</strong>
                                                    @if($setting->description)
                                                        <small class="d-block text-muted mb-2">{{ $setting->description }}</small>
                                                    @endif
                                                    
                                                    @include('admin.settings.partials.field', ['setting' => $setting])
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <!-- Notification Settings Tab -->
                            <div class="tab-pane fade" id="notification-tab" role="tabpanel">
                                <div class="row mt-4">
                                    @if(isset($settingsGroups['notification']))
                                        @foreach($settingsGroups['notification'] as $setting)
                                            <div class="col-xs-12 col-sm-12 col-md-6">
                                                <div class="form-group">
                                                    <strong>{{ $setting->label }}:</strong>
                                                    @if($setting->description)
                                                        <small class="d-block text-muted mb-2">{{ $setting->description }}</small>
                                                    @endif
                                                    
                                                    @include('admin.settings.partials.field', ['setting' => $setting])
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <!-- System Settings Tab -->
                            <div class="tab-pane fade" id="system-tab" role="tabpanel">
                                <div class="row mt-4">
                                    @if(isset($settingsGroups['system']))
                                        @foreach($settingsGroups['system'] as $setting)
                                            <div class="col-xs-12 col-sm-12 col-md-6">
                                                <div class="form-group">
                                                    <strong>{{ $setting->label }}:</strong>
                                                    @if($setting->description)
                                                        <small class="d-block text-muted mb-2">{{ $setting->description }}</small>
                                                    @endif
                                                    
                                                    @include('admin.settings.partials.field', ['setting' => $setting])
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Save Button -->
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 text-center pt-4">
                                <button type="submit" class="btn bg-gradient-dark btn-lg px-5">
                                    <i class="material-icons">save</i> Save Settings
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test Email Modal -->
<div class="modal fade" id="test-email-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Test Email Configuration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="test-email-form">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="test_email">Email Address:</label>
                        <input type="email" class="form-control" id="test_email" name="test_email" 
                               placeholder="Enter email address to send test email" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Test Email</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Import Settings Modal -->
<div class="modal fade" id="import-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('settings.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="settings_file">Settings File:</label>
                        <input type="file" class="form-control" id="settings_file" name="settings_file" 
                               accept=".json" required>
                        <small class="form-text text-muted">Select a JSON file exported from settings</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Import Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reset Confirmation Modal -->
<div class="modal fade" id="reset-modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to reset all settings to their default values?</p>
                <p class="text-danger"><strong>Warning:</strong> This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmReset()">Reset All Settings</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Auto-save settings on change
    let saveTimeout;
    $('#settings-form input, #settings-form select, #settings-form textarea').on('change', function() {
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(function() {
            // Optional: Add auto-save functionality here
        }, 2000);
    });
    
    // File input preview
    $('input[type="file"]').on('change', function() {
        const file = this.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Create preview if doesn't exist
                let preview = $(this).siblings('.file-preview');
                if (preview.length === 0) {
                    preview = $('<div class="file-preview mt-2"></div>');
                    $(this).parent().append(preview);
                }
                preview.html('<img src="' + e.target.result + '" style="max-width: 200px; max-height: 100px;" class="img-thumbnail">');
            }.bind(this);
            reader.readAsDataURL(file);
        }
    });
});

// Test Email Function
function testEmail() {
    $('#test-email-modal').modal('show');
}

$('#test-email-form').on('submit', function(e) {
    e.preventDefault();
    
    const email = $('#test_email').val();
    const btn = $(this).find('button[type="submit"]');
    const originalText = btn.html();
    
    btn.html('<i class="material-icons">hourglass_empty</i> Sending...').prop('disabled', true);
    
    $.ajax({
        url: '{{ route("settings.test-email") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            test_email: email
        },
        success: function(response) {
            if (response.success) {
                alert('Test email sent successfully to ' + email);
                $('#test-email-modal').modal('hide');
            } else {
                alert('Failed to send test email: ' + response.message);
            }
        },
        error: function() {
            alert('Error sending test email. Please check your email configuration.');
        },
        complete: function() {
            btn.html(originalText).prop('disabled', false);
        }
    });
});

// Clear Cache Function
function clearCache() {
    if (confirm('Are you sure you want to clear the application cache?')) {
        const btn = $('button[onclick="clearCache()"]');
        const originalText = btn.html();
        
        btn.html('<i class="material-icons">hourglass_empty</i> Clearing...').prop('disabled', true);
        
        $.ajax({
            url: '{{ route("settings.clear-cache") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert('Cache cleared successfully!');
                } else {
                    alert('Failed to clear cache: ' + response.message);
                }
            },
            error: function() {
                alert('Error clearing cache.');
            },
            complete: function() {
                btn.html(originalText).prop('disabled', false);
            }
        });
    }
}

// Export Settings Function
function exportSettings() {
    window.location.href = '{{ route("settings.export") }}';
}

// Reset Settings Function
function resetSettings() {
    $('#reset-modal').modal('show');
}

function confirmReset() {
    const form = $('<form>', {
        'method': 'POST',
        'action': '{{ route("settings.reset") }}'
    }).append($('<input>', {
        'type': 'hidden',
        'name': '_token',
        'value': '{{ csrf_token() }}'
    }));
    
    $('body').append(form);
    form.submit();
}

// Tab memory - remember active tab
$('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
    localStorage.setItem('activeSettingsTab', $(e.target).attr('href'));
});

// Restore active tab on page load
$(document).ready(function() {
    const activeTab = localStorage.getItem('activeSettingsTab');
    if (activeTab) {
        $('a[href="' + activeTab + '"]').tab('show');
    }
});
</script>
@endsection