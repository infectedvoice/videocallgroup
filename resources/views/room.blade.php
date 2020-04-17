@extends('layouts.app')
@section('content')
<div class="content">
    <div class="title m-b-md">
        Video Chat Rooms
    </div>

    <div id="media-div">
    </div>
</div>


<!— Insert just above the </head> tag —>
    <script src="https://media.twiliocdn.com/sdk/js/video/v1/twilio-video.min.js"></script>
    <script>
        
        // Twilio.Video.createLocalTracks({
        //     audio: true,
        //     video: {
        //         width: 300
        //     }
        // }).then(function(localTracks) {
        //     return 
        Twilio.Video.connect('{{ $accessToken }}', {
                name: '{{ $roomName }}',
                audio: true,
                video: {
                    width: 300
                }
   
        }).then(function(room) {
            console.log('Successfully joined a Room: ', room.name);

            room.participants.forEach(participantConnected);

            var previewContainer = document.getElementById(room.localParticipant.sid);
            if (!previewContainer || !previewContainer.querySelector('video')) {
                participantConnected(room.localParticipant);
            }

            room.on('participantConnected', function(participant) {
                console.log("Joining: ", participant.identity);
                participantConnected(participant);
            });

            room.on('participantDisconnected', function(participant) {
                console.log("Disconnect : ", participant.identity);
                participantDisconnected(participant);
            });
        });
        // additional functions will be added after this point
   
        function participantConnected(participant) {
            console.log('Participant "%s" connected', participant.identity);

            const div = document.createElement('div');
            div.id = participant.sid;
            div.setAttribute("style", "float: left; margin: 10px;");
            div.innerHTML = "<div style='clear:both'>"
            participant.identity, "</div>";

            participant.tracks.forEach(function(track) {
                trackAdded(div, track)
            });

            participant.on('trackAdded', function(track) {
                trackAdded(div, track)
            });
            participant.on('trackRemoved', trackRemoved);

            document.getElementById('media-div').appendChild(div);
        }

        function participantDisconnected(participant) {
            console.log('Participant "%s" disconnected', participant.identity);

            participant.tracks.forEach(trackRemoved);
            document.getElementById(participant.sid).remove();
        }

        function trackAdded(div, track) {
            div.appendChild(track.attach());
            var video = div.getElementsByTagName("video")[0];
            if (video) {
                video.setAttribute("style", "max-width:300px;");
            }
        }

        function trackRemoved(track) {
            track.detach().forEach(function(element) {
                element.remove()
            });
        }
    </script>
@endsection