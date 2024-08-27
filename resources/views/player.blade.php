<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
        <title>{{$video->name}} - Streamify360</title>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
        <script src="https://ssl.p.jwpcdn.com/player/v/8.13.0/jwplayer.js"></script>
        <script type="text/javascript">jwplayer.key="cLGMn8T20tGvW+0eXPhq4NNmLB57TrscPjd1IyJF84o=";</script>    
    </head>
    <body>
	    <div id="ykstream-player"></div>
    <script type="text/javascript">
    var player = jwplayer("ykstream-player");
    var config = {
        width: "100%",
        height: "100%",
        aspectratio: "16:9",
        autostart: false,
        controls: true,
        primary: "html5",
        abouttext: "Flash Player",
        aboutlink: "",
        image: "https://destro.tvlogy.to/__l-CX4tkLnNAm0GNNVz6FOBkAq2n5KcW1Nug3xcr8MANg0XvP_-iTCZJiNvNaGxveIkvbu3eTrzt077Osaavw/Sf6dPtWkIMYFV6rD77Bz5sBsxsewYI_JcYCU4e-LmJs/preview.jpg",
        sources: [{"file":"{{ $video->manifest_url }}","label":"HD","type":"application/x-mpegURL"}],
        tracks: [{"file":"https://destro.tvlogy.to/6BJHrHrCQTX-0SR8PWYEvfwsKv2m_eFbD20A5B5UnsB9e3EQgG_BIGs_wH0KAnG9d7oNRxj1fA5MLW_m8kx_ZQ/XZ7g29lzbmhj5v5KSvotvsxtZEy1YnFKWu42SnDqtWc/thumbnail.vtt","kind":"thumbnails"}],
        logo: {
            file: "",
            link: "",
            position: "top-left",
        },
        captions: {
            color: "#FFFFFF",
            fontSize: "14",
            fontFamily: "Trebuchet MS, Sans Serif",
            backgroundColor: "",
        },
        advertising: {
            client: "googima",
            schedule: "https://flow.tvlogy.to/ad/vast-embed020.xml"
        }
    };
    player.setup(config);</script>

    </body>
</html>
