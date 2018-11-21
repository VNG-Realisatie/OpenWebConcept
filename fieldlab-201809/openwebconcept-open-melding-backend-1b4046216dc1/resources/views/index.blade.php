<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Open Melding</title>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link rel='stylesheet' href="{{ asset('css/app.css') }}" type='text/css' media='all' />
</head>
<body>
<div class="container-fluid page-holder">
    <header class="container-fluid header--navigation">
        <div class="container">
            <div class="logo-wrapper">
                <a href="/" alt="logo"></a>
            </div>
            <div class="menu-wrapper d-none">
                <nav class="navbar-top">
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                        <div class="navbar-nav">
                            <!-- <a class="nav-item nav-link" href="#">Link 1</a>
                            <a class="nav-item nav-link" href="#">Link 2</a> -->
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </header>
    <div class="container--pages">

        <!-- First page -->
        <section class="section--page active" data-page="1">

            <div class="row m-0 height--100">
                <div class="col-lg p-0">
                    <div id="leaflet_map"></div>
                    <a href="#" class="btn btn-success btn--fixed btn--bottom_150 goto--page" data-goto="2" data-validate="true">Maak een melding</a>
                </div>
            </div>

        </section>

        <form action="" method="POST" class="ajax--form_wrapper">

            <!-- Second page -->
            <section class="section--page container-fluid" data-page="2">
                <div class="form-group intro--text address--display_name">
                    <span>Bedankt, je maakt een melding voor <strong id="intro--address_name"></strong></span>
                </div>
                <div class="form-group">
                    <div class="upload-preview">
                        <div class="upload-preview_container">
                            <img src="">
                        </div>
                        <a href="#" class="text-primary reset--upload">verwijder afbeelding</a>
                    </div>
                    <div class="upload-btn-wrapper">
                        <button class="btn"><i class="fas fa-camera upload-icon"></i>Maak/upload een foto</button>
                        <input type="file" id="input--picture" name="picture" accept="image/*">
                    </div>
                </div>
                <div class="form-group">
                    <textarea rows="4" id="input--message" class="form-control" name="message" placeholder="Omschrijf de situatie: wat wilt u anders zien?"></textarea>
                </div>
                <div class="form-group">
                    <div class="radio--group">
                        <label class="active">
                            <input type="radio" class="input--date_type" name="date_type" value="now">
                            Ik ben er nu
                        </label>
                        <label>
                            <input type="radio" class="input--date_type" name="date_type" value="past">
                            Ik was hier eerder
                        </label>
                    </div>
                </div>
                <div class="form-group date-select-wrapper">
                    <div class="input--group">
                        <input type="date" name="date" placeholder="dd-mm-jjjj" id="input--date" class="form-control">
                        <input type="time" name="time" placeholder="--:--" id="input--time" class="form-control">
                    </div>
                </div>
                <div class="section--button_container">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: 33%" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="container-fluid">
                        <a href="#" class="btn btn-secondary btn--left goto--page" data-goto="1" data-validate="false"><i class="fas fa-arrow-left"></i><span>Vorige</span></a>
                        <a href="#" class="btn btn-success btn--right goto--page" data-goto="3" data-validate="true"><span>Volgende</span><i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </section>

            <!-- Third page -->
            <section class="section--page section--submit_page container-fluid" data-page="3">
                <div class="form-group form-group--centered">
                    <label class="strong">Mogen we u bellen voor vragen?<small>(niet verplicht)</small></label>
                    <input type="text" name="phone" id="input--phone" class="form-control" placeholder="Vul hier uw telefoonnummer in">
                </div>
                <div class="form-group form-group--centered">
                    <label class="strong">Wilt u op de hoogte gehouden worden?<small>(niet verplicht)</small></label>
                    <input type="email" name="email" id="input--email" class="form-control" placeholder="Vul hier uw e-mail adres in">
                </div>
                <div class="section--button_container">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: 66%" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="container-fluid">
                        <a href="#" class="btn btn-secondary btn--left goto--page" data-goto="2" data-validate="false"><i class="fas fa-arrow-left"></i><span>Vorige</span></a>
                        <a href="#" class="btn btn-success btn--right ajax--send_form"><span>Verstuur</span><i class="fas fa-check"></i></a>
                    </div>
                </div>
            </section>

            <section class="section--page section--thanks container-fluid" data-page="thanks">
                <div class="thanks--well">
                    <strong>Bedankt,</strong>
                    <p>samen houden we</p>
                    <p>Nederland leefbaar</p>
                    <p>voor iedereen</p>
                </div>
            </section>

            <input type="hidden" name="location[lat]" id="input--location_lat">
            <input type="hidden" name="location[lng]" id="input--location_lng">

        </form>

    </div>

</div>

<div class="overlay--loading">
    <strong>Bezig met het versturen...</strong>
</div>

<script src="{{ asset('js/app.min.js') }}"></script>

</body>
</html>