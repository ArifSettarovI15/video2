#!/bin/bash
echo 'Start bower post install';

grunt copy:vendors;

echo 'Vendors have been copied';

cd 'bower_components';

for file in $(find . -name '*.css');
do
    if [[ -f ${file} ]]; then
        if ! [[ -f ${file%.css}'.scss' ]]; then
            cp ${file} ${file%.css}'.scss'
            echo 'ok - '${file};
        fi
        #rm ${file};
    fi
done

echo 'Sass files generated';

