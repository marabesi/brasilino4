hackpi.factory('brasilinoSocket', ['$websocket', function($websocket) {
    var url = 'ws://echo.websocket.org';
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
    $scope.moveLeft = function() {
        brasilinoSocket.$emit('pong', 'EE');
    };
    
    $scope.moveRight = function() {
        brasilinoSocket.$emit('pong', 'DD');
    };
    
    $scope.moveUp = function() {
        brasilinoSocket.$emit('pong', 'AA');
    };
    
    $scope.moveDown = function() {
        brasilinoSocket.$emit('pong', 'FF');
    };
    
    $scope.close = function() {
        brasilinoSocket.$close();
        console.log('Connection closed');
    }

}]);