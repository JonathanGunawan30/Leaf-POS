<script>
    document.addEventListener('contextmenu', event => event.preventDefault());
        let isLoading = true;
        window.addEventListener('load', () => {
            isLoading = false;
        });
        document.onkeydown = function(e) {
            if (isLoading) return true;
            if (e.keyCode === 123 || // F12
                (e.ctrlKey && e.shiftKey && e.keyCode === 73) || // Ctrl+Shift+I
                (e.ctrlKey && e.shiftKey && e.keyCode === 74) || // Ctrl+Shift+J
                (e.ctrlKey && e.shiftKey && e.keyCode === 67) || // Ctrl+Shift+C
                (e.ctrlKey && e.keyCode === 85)) { // Ctrl+U
                alert("Don't inspect if you don't want to be blocked !");
                    return false;
                }
            };
            // DevTools Detection
            let devtools = function() {};
            devtools.toString = function() {
                if (!isLoading) {
                    alert("Don't inspect if you don't want to be blocked !");
                }
                return '';
            }
            console.log('%c', devtools);
            // Disable source view
            document.onkeypress = function (event) {
                if (isLoading) return true;
                event = (event || window.event);
                if (event.keyCode == 123) {
                    alert("Don't inspect if you don't want to be blocked !");
                    return false;
                }
            };
</script>