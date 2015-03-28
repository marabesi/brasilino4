hackpi.factory('brasilinoSocket', ['$websocket', function($websocket) {
    var url = 'ws://192.168.0.105:9002';
    var brasilinoWs = $websocket.$new(url); 

    brasilinoWs.$on('$open', function() {
        console.log('Conected to : ' + url);
    });

    brasilinoWs.$on('pong', function(data){
        console.log('server : ' + data);
    });
    
    return brasilinoWs;
}]);

hackpi.controller('MainController', ['$scope', 'brasilinoSocket', function($scope, brasilinoSocket) {
    $scope.move = function(direction) {
        brasilinoSocket.$emit('pong', direction);
    };
    
    $scope.close = function() {
        brasilinoSocket.$close();
        console.log('Connection closed');
    }

}]);