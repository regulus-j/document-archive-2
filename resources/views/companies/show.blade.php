<<<<<<< HEAD
@extends('layouts.app')

@section('content')
<div class="container py-3">
    <div class="row justify-content-center">
        <div class="col-md-10">
        <div class="flex items-center space-x-3">              
        </div>           
        <div class="card">
                <div class="card-body">
                    <!-- Company Information -->
                    <div class="mb-4">
                        <h5 class="card-title mb-4">Organization Information</h5>
                        <div class="row mb-3">
                            <div class="col-md-4 font-weight-bold">Organization Name:</div>
                            <div class="col-md-8">{{ $company->company_name }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 font-weight-bold">Email:</div>
                            <div class="col-md-8">{{ $company->company_email }}</div>
                        </div>
                        
                    </div>

                    <!-- Company Owner -->
                    @if($company->owner)
                    <div class="mb-4">
                        <h5 class="card-title mb-4">Organization Owner</h5>
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3">
                                        <div class="avatar bg-primary text-white rounded-circle p-2">
                                            {{ substr($company->owner->first_name, 0, 1) }}{{ substr($company->owner->last_name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">{{ $company->owner->first_name }} {{ $company->owner->last_name }}</h6>
                                        <p class="text-muted mb-0">{{ $company->owner->email }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Team Members -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Team Members</h5>
                            @can('manage', $company)
                                <a href="{{ route('users.create', ['company_id' => $company->id]) }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-plus"></i> Add Member
                                </a>
                            @endcan
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   
                                   
                                    @foreach($company->users as $user)


                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-primary text-white rounded-circle mr-2" style="width: 30px; height: 30px; font-size: 12px; display: flex; align-items: center; justify-content: center;">
                                                    {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                                </div>
                                                {{ $user->first_name }} {{ $user->last_name }}
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @foreach($user->roles as $role)
                                                <span class="badge bg-primary">{{ $role->name }}</span>
                                            @endforeach
                                        </td>
                                       
                                            <div class="btn-group" role="group">
                                                @can('view', $user)
                                                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('update', $user)
                                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
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

@push('styles')
<style>
    .avatar {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: 600;
    }
    .card {
        box-shadow: 0 2px 4px rgba(0,0,0,.05);
    }
</style>
@endpush
=======
@extends('layouts.app')

@section('content')
<div class="container py-3">
    <div class="row justify-content-center">
        <div class="col-md-10">
        <div class="flex items-center space-x-3">              
        </div>           
        <div class="card">
                <div class="card-body">
                    <!-- Company Information -->
                    <div class="mb-4">
                        <h5 class="card-title mb-4">Organization Information</h5>
                        <div class="row mb-3">
                            <div class="col-md-4 font-weight-bold">Organization Name:</div>
                            <div class="col-md-8">{{ $company->company_name }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 font-weight-bold">Email:</div>
                            <div class="col-md-8">{{ $company->company_email }}</div>
                        </div>
                        
                    </div>

                    <!-- Company Owner -->
                    @if($company->owner)
                    <div class="mb-4">
                        <h5 class="card-title mb-4">Organization Owner</h5>
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3">
                                        <div class="avatar bg-primary text-white rounded-circle p-2">
                                            {{ substr($company->owner->first_name, 0, 1) }}{{ substr($company->owner->last_name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">{{ $company->owner->first_name }} {{ $company->owner->last_name }}</h6>
                                        <p class="text-muted mb-0">{{ $company->owner->email }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Team Members -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">Team Members</h5>
                            @can('manage', $company)
                                <a href="{{ route('users.create', ['company_id' => $company->id]) }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-plus"></i> Add Member
                                </a>
                            @endcan
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   
                                   
                                    @foreach($company->users as $user)


                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-primary text-white rounded-circle mr-2" style="width: 30px; height: 30px; font-size: 12px; display: flex; align-items: center; justify-content: center;">
                                                    {{ substr($user->first_name, 0, 1) }}{{ substr($user->last_name, 0, 1) }}
                                                </div>
                                                {{ $user->first_name }} {{ $user->last_name }}
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @foreach($user->roles as $role)
                                                <span class="badge bg-primary">{{ $role->name }}</span>
                                            @endforeach
                                        </td>
                                       
                                            <div class="btn-group" role="group">
                                                @can('view', $user)
                                                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                @can('update', $user)
                                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
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

@push('styles')
<style>
    .avatar {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: 600;
    }
    .card {
        box-shadow: 0 2px 4px rgba(0,0,0,.05);
    }
</style>
@endpush
>>>>>>> 8f98270b7e7901b3262bb54dfd67342b9b6f2530
