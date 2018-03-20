@extends('layouts.skeleton')

@section('content')
<script src="/vendor/u2f/u2f-api.js" type="text/javascript"></script>
<div class="settings">

  {{-- Breadcrumb --}}
  <div class="breadcrumb">
    <div class="{{ auth()->user()->getFluidLayout() }}">
      <div class="row">
        <div class="col-xs-12">
          <ul class="horizontal">
            <li>
              <a href="/dashboard">{{ trans('app.breadcrumb_dashboard') }}</a>
            </li>
            <li>
              <a href="/settings">{{ trans('app.breadcrumb_settings') }}</a>
            </li>
            <li>
              {{ trans('app.breadcrumb_settings_security') }}
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="{{ auth()->user()->getFluidLayout() }}">
    <div class="row">

      @include('settings._sidebar')

      <div class="col-xs-12 col-md-9">

        <div class="br3 ba b--gray-monica bg-white mb4">
          <div class="pa3 bb b--gray-monica">

            <u2f-connector
              :currentkeys="{{ json_encode($currentKeys) }}"
              :registerdata="{{ json_encode($registerData) }}"
              :method="'register'">
            </u2f-connector>

              <a href="/settings/security" class="btn">{{ trans('app.cancel') }}</a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
