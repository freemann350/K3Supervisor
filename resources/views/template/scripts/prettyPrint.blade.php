<script>
    function prettyPrint() {
        var ugly = document.getElementById('resource').value;
        var obj = JSON.parse(ugly);
        var pretty = JSON.stringify(obj, undefined, 4);
        document.getElementById('resource').value = pretty;
    }
</script>