var config = (function (){
    // Variable global que sirve para indicar si los mensajes
    // de ayuda se imprimirán a consola y en alert
    var debugging = false;
    
    return {
        isDebugging: function (){
            if (debugging) {
                return true;
            }
            
            return false;
        }
    }
})();
