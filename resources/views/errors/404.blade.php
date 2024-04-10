

<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style type="text/css">
:root {
  --color: 91 , 200, 109
}
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&display=swap');
body { font-family: 'Montserrat', sans-serif;margin: 0; overflow: hidden;
background-position: right !important;
background-repeat: no-repeat !important;
background-color: rgba({{getClientPreferenceDetail()->site_top_header_color ?? '#ffffff'}}, .1) !important;
 }


h1, h2, h3, h4, h5, h6 {
  font-family: "Montserrat", sans-serif; }
.loader-wrapper {
    height: 100vh;
    width: 100vw;
    display: flex;
    background-color: #ffffff;
    -webkit-box-align: center;
    -ms-flex-align: center;
    align-items: center;
    -webkit-box-pack: center;
    -ms-flex-pack: center;
    justify-content: center;
    position: fixed;
    z-index: 9;
    top: 0;
}

.loader {
    border: 16px solid #111;
    border-radius: 50%;
    border-top: 16px double #5076db;
    border-bottom: 16px double #5076db;
    width: 80px;
    height: 80px;
    -webkit-animation: spin 2s linear infinite;
    animation: spin 2s linear infinite;
}
@-webkit-keyframes spin {
    0% {
        -webkit-transform: rotate(0deg);
    }
    100% {
        -webkit-transform: rotate(360deg);
    }
}
@keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

.page-section .content-detail {
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}
.page-section .content-detail .sub-title,
.page-section .content-detail .detail-text {
    display: block;
    color: rgb(24, 6, 6);
    text-align: center;
}
.page-section .content-detail .sub-title {
    margin-bottom: 15px;
    font-weight: 600;
    font-size: 30px;
}
.page-section .content-detail .global-title {
    display: block;
    color: {{ getClientPreferenceDetail()->web_color??'#fff' }};
    font-size: 150px;
    font-weight: 800;
    margin: 0;
}

.page-section .content-detail .global-title span {
animation-name: animateblur;
animation-duration: 1.5s;
animation-timing-function:linear;
animation-fill-mode: forwards;
}
.page-section .content-detail .global-title span:nth-child(1) {
animation-delay: 1s; }
.page-section .content-detail .global-title span:nth-child(2) {
animation-delay: 2s; }
.page-section .content-detail .global-title span:nth-child(3) {
animation-delay: 3s; }

@keyframes animateblur {
    0% {
        opacity: 0;
        filter: blur(10px);
    }
    100% {
        opacity: 1;
        filter: blur(0px);
    }
}
.page-section .content-detail .back-btn {
    margin-top: 15px;
}
.page-section .content-detail .back-btn .btn {
    padding: 8px 15px;
    color: #f55d42;
    border: 1px solid #f55d42;
    border-radius: 30px;
    font-size: 14px;
    text-decoration: none;
}
.page-section .content-detail .back-btn .btn:hover {
    background-color: #f55d42;
    color: #fff;
}
.background_color {
    position: fixed;
    z-index: -1;
    height: 200%;
    width: 40%;
    top: 0;
    right: 0;
    transform: rotate(20deg);
    opacity: .3;
}

@media (max-width: 768px) {
    .page-section .content-detail {
        padding: 0 15px;
    }
    .page-section .content-detail .global-title {
        font-size: 120px;
        font-weight: 700;
    }
    .page-section .content-detail .sub-title {
        font-weight: 500;
        font-size: 25px;
    }
    .page-section .content-detail .detail-text {
        font-size: 14px;
    }
}

@media (max-width: 480px) {
    .page-section .content-detail {
        padding: 0 15px;
    }
    .page-section .content-detail .global-title {
        font-size: 80px;
        font-weight: 600;
    }
    .page-section .content-detail .sub-title {
        font-weight: 500;
        font-size: 22px;
    }
}
    </style>
 

</head>
<body>
    <div class="loader-wrapper" id="loader-wrapper" style="display: none;">
        <div class="loader"></div>
    </div>
    <section class="page-section">
        <div class="full-width-screen">
            <div class="container-fluid">
                <div class="content-detail">
                    <h1 class="global-title"><span>4</span><span>0</span><span>4</span></h1>

                    <h4 class="sub-title">Oops!</h4>

                    <p class="detail-text">We're sorry,<br> The page you were looking for doesn't exist anymore.</p>

                    <div class="back-btn">
                        
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="background_color"></div>
<script type="text/javascript" class="bg">
    var green = '{{getClientPreferenceDetail()->site_top_header_color ?? "#ffffff"}}';
    var red = '{{getClientPreferenceDetail()->site_top_header_color ?? "#ffffff"}}';
    var svg = '<svg width="393" height="393" viewBox="0 0 393 393" fill="none" xmlns="http://www.w3.org/2000/svg"><rect x="154.149" y="0.691137" width="283" height="283" rx="69.5" transform="rotate(32.7999 154.149 0.691137)" stroke="'+red+'"/><rect x="160.591" y="33.1011" width="236.275" height="236.275" rx="70" transform="rotate(32.7999 160.591 33.1011)" fill="'+green+'"/></svg>';
        var encoded = window.btoa(svg);
        document.getElementsByClassName("background_color")[0].style.background = "url(data:image/svg+xml;base64,"+encoded+")";
</script>


</body>
</html>
