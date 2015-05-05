brasilino4.factory('brasilinoSocket', ['$websocket', 'brasilino4Settings', function($websocket, brasilino4Settings) {
    var url = 'ws://' + brasilino4Settings.ipSocket;
    var brasilinoWs = $websocket.$new(url);

    brasilinoWs.$on('$open', function() {
        console.log('Connected to : ' + url);
    });

    brasilinoWs.$on('pong', function(data){
        console.log('Data received : ' + data);
    });

    return brasilinoWs;
}]);

brasilino4.controller('MainController', ['$scope', 'brasilinoSocket',
'brasilino4Settings', '$interval', function($scope, brasilinoSocket, brasilino4Settings, $interval) {
    $scope.cameraAddress = 'http://' + brasilino4Settings.ipCamera;

    var delay = 200;
    var timer;

    $scope.move = function(direction) {
        if (!timer) {
            timer = $interval(function () {
                brasilinoSocket.$emit('pong', direction);
                console.log('Holding ' + direction);
            }, delay);
        }
    };

    $scope.release = function(direction) {
        if (timer) {
            $interval.cancel(timer);
            timer = null;
            brasilinoSocket.$emit('pong', direction);
            console.log('Releasing ' + direction);
        }
    }

    $scope.close = function() {
        console.log('Connection has closed');
        brasilinoSocket.$close();
    }

}]);
