#!/bin/bash

if [[ "$1" == "start" ]]
then
    sh scripts/start.sh
    sh scripts/post-npm.sh
fi

if [[ "$1" == "backend" || "$1" == "frontend" ]]
then

    if [[ "$2" == "component" || "$2" == "module" || "$2" == "section" || "$2" == "page" || "$2" == "element" ]]
    then

        #
        APP_FOLDER=$1;
        ELEMENT=$2;
        COMMAND=$3;
        NAME=$4;
        NEW_NAME=$5;

        DIR=../app/views/${APP_FOLDER}/${ELEMENT}s/${NAME};
        if [[ ${ELEMENT} == "module" ]]
        then
             DIR=../app/views/${ELEMENT}s/${NAME};
        fi


        if [[ ${COMMAND} == "add" ]]
        then
            if ! [ -d ${DIR} ]; then
                mkdir -p ${DIR}
                touch ${DIR}/${NAME}.twig

                if [[ ${ELEMENT} == "component" || ${ELEMENT} == "module" ]]
                then
                     touch ${DIR}/_${NAME}_main.js
                     touch ${DIR}/_${NAME}_func.js
                fi

                if [[ ${ELEMENT} == "component" || ${ELEMENT} == "section" || ${ELEMENT} == "page" || ${ELEMENT} == "element" ]]
                then
                     touch ${DIR}/_${NAME}.scss
                elif [[ ${ELEMENT} == "module" ]]
                then
                    touch ${DIR}/_${NAME}-core.scss
                    touch ${DIR}/_${NAME}-custom.scss
                fi

                mkdir -p ${DIR}/images
                touch ${DIR}/images/.keep
               echo ${ELEMENT}' has been created';
            else
               echo ${ELEMENT}' already exists';
            fi
        fi

        if [[ ${COMMAND} == "rename" ]]
        then
             NEW_DIR=../app/views/${APP_FOLDER}/${ELEMENT}s/${NEW_NAME};
            if [[ ${ELEMENT} == "module" ]]
            then
                 NEW_DIR=../app/views/${ELEMENT}s/${NEW_NAME};
            fi

            if [ -d ${DIR} ]; then
                mv  ${DIR} ${NEW_DIR}
                mv ${NEW_DIR}/${NAME}.twig ${NEW_DIR}/${NEW_NAME}.twig

                if [[ ${ELEMENT} == "component" || ${ELEMENT} == "module" ]]
                then
                    mv ${NEW_DIR}/_${NAME}_main.js ${NEW_DIR}/_${NEW_NAME}_main.js
                    mv ${NEW_DIR}/_${NAME}_func.js ${NEW_DIR}/_${NEW_NAME}_func.js
                fi

                 if [[ ${ELEMENT} == "component" || ${ELEMENT} == "section"  || ${ELEMENT} == "page"  || ${ELEMENT} == "element" ]]
                then
                     mv ${NEW_DIR}/_${NAME}.scss ${NEW_DIR}/_${NEW_NAME}.scss
                elif [[ ${ELEMENT} == "module" ]]
                then
                    mv ${NEW_DIR}/_${NAME}-core.scss ${NEW_DIR}/_${NEW_NAME}-core.scss
                    mv ${NEW_DIR}/_${NAME}-custom.scss ${NEW_DIR}/_${NEW_NAME}-custom.scss
                fi

               echo ${ELEMENT}' has been renamed';
            else
               echo ${ELEMENT}' not exists';
            fi
        fi

        if [[ ${COMMAND} == "delete" ]]
        then
            if [ -d ${DIR} ]; then
                rm -rf ${DIR}
               echo ${ELEMENT}' has been deleted';
            else
               echo ${ELEMENT}' not exists';
            fi
        fi
    fi
fi