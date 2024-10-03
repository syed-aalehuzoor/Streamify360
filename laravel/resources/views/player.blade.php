<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>{{ $video->name }} - Streamify360</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/brands/streamify360.png') }}">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>
    <script src="https://ssl.p.jwpcdn.com/player/v/8.13.0/jwplayer.js"></script>
    
    <script type="text/javascript">
        jwplayer.key = "fsEKyI5f7mNCCzmTSj7cHPQwradnhMGhL8VxSsVPRMs=";
    </script>
</head>
<body style="margin: 0%;">
    <div id="video_player"></div>
    <script type="text/javascript">
        var player = jwplayer("video_player");
        var config = {
            width: "{!! $settings->responsive ? '100%' : ($settings->player_width ? $settings->player_width : '100%') !!}",
            height: "{!! $settings->responsive ? '100%' : ($settings->player_height ? $settings->player_height : '100%') !!}",
            aspectratio: "16:9",
            autostart: {{ $settings->autoplay ? 'true,mute: true' : 'false' }},
            controls: {{ $settings->show_controls ? 'true' : 'false' }},
            primary: "html5",
            abouttext: "Streamify360",
            aboutlink: "https://streamify360.com",
            image: "{{ $video->thumbnail_url }}",
            @if ($settings->show_playback_speed)
                playbackRateControls: [0.5, 1, 1.5, 2],        
            @endif
            sources: [{"file":"{{ $video->manifest_url }}","label":"HD","type":"application/x-mpegURL"}],
            tracks: [{"file":"","kind":"thumbnails"}],
            logo: {
                file: "",
                link: "",
                position: "top-left",
            },
            captions: {
                color: "#FFFFFF", // Default caption color
                fontSize: "14",
                fontFamily: "Trebuchet MS, Sans Serif",
                backgroundColor: "",
            },
            advertising: {
                client: "",
                schedule: ""
            },
            skin: {
                controlbar: {
                    background: "{{ $settings->controlbar_background_color ?? 'rgba(0, 0, 0, 0.7)'}}", // Control bar background color
                    icons: "{{ $settings->controlbar_icons_color ?? 'rgba(255, 255, 255, 0.8)'}}", // Icon color when inactive
                    iconsActive: "{{ $settings->controlbar_icons_active_color ?? '#FFFFFF'}}", // Icon color when active
                    text: "{{ $settings->controlbar_text_color ?? '#FFFFFF'}}" // Control bar text color
                },
                menus: {
                    background: "{{ $settings->menu_background_color ?? '#333333'}}", // Menu background color
                    text: "{{ $settings->menu_text_color ?? 'rgba(255, 255, 255, 0.8)'}}", // Inactive text color in menus
                    textActive: "{{ $settings->menu_text_active_color ?? '#FFFFFF'}}" // Active text color in menus
                },
                timeslider: {
                    progress: "{{ $settings->timeslider_progress_color ?? '#F2F2F2'}}", // Progress bar color
                    rail: "{{ $settings->timeslider_rail_color ?? 'rgba(255, 255, 255, 0.3)'}}" // Rail color of the time slider
                },
                tooltips: {
                    background: "{{ $settings->tooltip_background_color ?? '#000000'}}", // Tooltip background color
                    text: "{{ $settings->tooltip_text_color ?? '#FFFFFF'}}" // Tooltip text color
                }
            }
        };
        player.setup(config);
        // Set default playback speed after setup
        player.on('ready', function() {
            player.setPlaybackRate({{ str_replace('x', '', $settings->playback_speed) }}); // Set the desired default playback speed
            player.setVolume({{ $settings->volume_level }}); // Set the default volume to 50%
            player.addButton(
                "{{ $settings->logo_url }}",
                "Streamify360",
                function () {
                    var win = window.open("{{ $settings->website_url }}", "_blank");
                    win.focus();
                },
                "Streamify360"
            );
            @if ($settings->social_sharing_enabled) // Adjust the condition as per your settings structure
                player.addButton(
                    "{{ asset('storage/brands/fbshare.png') }}",
                    "Share on Facebook",
                    function () {
                        var win = window.open("https://www.facebook.com/sharer/sharer.php?u={{ $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] }}", "_blank");
                        win.focus();
                    },
                    "Facebook"
                );
            @endif

        });
    </script>
    <script src="res/amodal.js" type="text/javascript"></script>
</body>
</html>
