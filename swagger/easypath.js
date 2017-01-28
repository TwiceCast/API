var $RefParser = require('json-schema-ref-parser');

$RefParser.dereference('paths.yaml', function(err, schema) {
    if (err) {
        console.error(err);
    } else {
        for (i in schema) {
            path = schema[i];
            console.log(i);
            for (j in path) {
                method = path[j];
                console.log(j);
            }
        }
    }
});
