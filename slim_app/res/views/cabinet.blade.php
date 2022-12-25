@extends('layout')

@section('content')
<script>
(function(w,d,s,g,js,fjs){
  g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(cb){this.q.push(cb)}};
  js=d.createElement(s);fjs=d.getElementsByTagName(s)[0];
  js.src='https://apis.google.com/js/platform.js';
  fjs.parentNode.insertBefore(js,fjs);js.onload=function(){g.load('analytics')};
}(window,document,'script'));
</script>
<div>
<p>
<a href="/post" class="font-bold py-2 px-4 rounded bg-green-400">Post</a>
</p>
</div>
<section id="auth-button"></section>
<section id="view-selector"></section>
<section id="timeline"></section>
<script>
gapi.analytics.ready(function() {
  console.log('*gapi.analytics.ready');
/*
  // Step 3: Authorize the user.

  var CLIENT_ID = '123-FOOBAR.apps.googleusercontent.com';

  gapi.analytics.auth.authorize({
    container: 'auth-button',
    clientid: CLIENT_ID,
  });

  // Step 4: Create the view selector.

  var viewSelector = new gapi.analytics.ViewSelector({
    container: 'view-selector'
  });

  // Step 5: Create the timeline chart.

  var timeline = new gapi.analytics.googleCharts.DataChart({
    reportType: 'ga',
    query: {
      'dimensions': 'ga:date',
      'metrics': 'ga:sessions',
      'start-date': '30daysAgo',
      'end-date': 'yesterday',
    },
    chart: {
      type: 'LINE',
      container: 'timeline'
    }
  });

  // Step 6: Hook up the components to work together.

  gapi.analytics.auth.on('success', function(response) {
    viewSelector.execute();
  });

  viewSelector.on('change', function(ids) {
    var newIds = {
      query: {
        ids: ids
      }
    }
    timeline.set(newIds).execute();
  });*/
});
</script>

{{--
<div class="main-content">
  <div class="container-fluid">
      <div class="row" style="justify-content: center;">
          <div class="col-md-8">
              <div class="card">
                  <div class="card-header card-header-danger">
                      <h4 class="card-title">Edit Profile</h4>
                      <p class="card-category">Complete your profile</p>
                  </div>
                  <div class="card-body">
                      <form>
                          <div class="row">
                              <div class="col-md-5">
                                <mat-form-field class="example-full-width">
                                    <input matInput placeholder="Company (disabled)" disabled>
                                  </mat-form-field>
                              </div>
                              <div class="col-md-3">
                                  <mat-form-field class="example-full-width">
                                    <input matInput placeholder="Username">
                                  </mat-form-field>
                              </div>
                              <div class="col-md-4">
                                  <mat-form-field class="example-full-width">
                                    <input matInput placeholder="Email address" type="email">
                                  </mat-form-field>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-md-6">
                                <mat-form-field class="example-full-width">
                                  <input matInput placeholder="Fist Name" type="text">
                                </mat-form-field>
                              </div>
                              <div class="col-md-6">
                                <mat-form-field class="example-full-width">
                                  <input matInput placeholder="Last Name" type="text">
                                </mat-form-field>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-md-12">
                                <mat-form-field class="example-full-width">
                                  <input matInput placeholder="Adress" type="text">
                                </mat-form-field>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-md-4">
                                <mat-form-field class="example-full-width">
                                  <input matInput placeholder="City" type="text">
                                </mat-form-field>
                              </div>
                              <div class="col-md-4">
                                <mat-form-field class="example-full-width">
                                  <input matInput placeholder="Country" type="text">
                                </mat-form-field>
                              </div>
                              <div class="col-md-4">
                                <mat-form-field class="example-full-width">
                                  <input matInput placeholder="Postal Code" type="text">
                                </mat-form-field>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-md-12">
                                <label>About Me</label>
                                <mat-form-field class="example-full-width">
                                   <textarea matInput placeholder="Lamborghini Mercy, Your chick she so thirsty, I'm in that two seat Lambo."></textarea>
                                 </mat-form-field>
                                  <!-- <div class="form-group">

                                      <div class="form-group">
                                          <label class="bmd-label-floating"> Lamborghini Mercy, Your chick she so thirsty, I'm in that two seat Lambo.</label>
                                          <textarea class="form-control" rows="5"></textarea>
                                      </div>
                                  </div> -->
                              </div>
                          </div>
                          <button mat-raised-button type="submit" class="btn btn-danger pull-right">Update Profile</button>
                          <div class="clearfix"></div>
                      </form>
                  </div>
              </div>
          </div>
      </div>
  </div>
</div>
--}}

@endsection
