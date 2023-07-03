#!/bin/bash

# Make sure we exit on error
set -e pipefail

CURRENT_PATH=$(pwd)
INSTALL_PATH=~/Domains
PROJECT_FOLDER=github_mmarucci33
PROJECT_PATH="$INSTALL_PATH/$PROJECT_FOLDER"
PATH_SRC="$PROJECT_PATH/src"

check_has() {
    type "$1" >/dev/null 2>&1
}

requirements_validate(){
    local errors
    echo  "Requirements validation" 2>/dev/null

    if ! check_has docker; then
        errors="$errors\n==> You need to have 'docker'"
        errors="$errors\n===> Review: https://docs.docker.com/engine/install/"
    fi
    if ! check_has docker-compose && ! check_has compose ; then
        errors="$errors\n==> You need to have 'composer' from 'docker'"
        errors="$errors\n===> Review: https://docs.docker.com/compose/"
    fi


    if  [ ! -z "$errors" ]; then
      echo  "The following problems were detected:" 2>/dev/null
      echo -e "$errors" 2>/dev/null
      exit 1
    fi

    echo "=> Requirements OK" 2>/dev/null
}

stop_services(){
    echo "=> Stop services" 2>/dev/null

    echo "==> Stop apache" 2>/dev/null
    sudo service apache2 stop

    echo "==> Stop all containers" 2>/dev/null
    docker stop $(docker ps -aq)
}

create_folder_install(){
    echo "=> Create folder $PROJECT_PATH" 2>/dev/null
    mkdir -p $PROJECT_PATH
}

markshust_install_docker() {
    cd "$PROJECT_PATH"
    curl -s https://raw.githubusercontent.com/markshust/docker-magento/master/lib/template | bash
}

clone_repo(){
    echo "==> Clone project" 2>/dev/null
    git clone -b main git@github.com:mmaximo33/challenge_tm.git "$PATH_SRC"

    if ! [ -d "$PATH_SRC" ]; then
        echo "The project was not cloned in $PATH_SRC" 2>/dev/null
        exit 1
    fi
}

db_prepare(){
    echo "==> Prepare database" 2>/dev/null
    local PATH_DB
    local PATH_DB_OUTPUT
    PATH_DB="${PATH_SRC}/dump/magento.gz"

    if  ! [ -f "$PATH_DB" ] ; then
        PATH_DB="${PATH_SRC}/dump/magento.sql.gz"
        if ! [ -f "$PATH_DB" ] ; then
            echo "===> Db dont exists" 2>/dev/null
            exit 1
        fi
    fi

    echo "===> Unzip database" 2>/dev/null
    PATH_DB_OUTPUT="$PATH_SRC/dump/magento.sql"
    zcat "$PATH_DB" > "$PATH_DB_OUTPUT"

    echo "===> Fixed database" 2>/dev/null
    echo "====> This may take a few minutes (avg 20m). Wait..." 2>/dev/null
    sed -i 's/DEFINER=[^*]*\*/\*/g' "$PATH_DB_OUTPUT"
}

markshust_preimport_db(){
    cd "$PROJECT_PATH"
    bin/start --no-dev
    bin/copytocontainer --all
    bin/composer install
}

markshust_db_import(){
    echo "==> Import database" 2>/dev/null
    echo "===> This may take a few minutes (avg 30m). Wait..." 2>/dev/null
    cd "$PROJECT_PATH"
    bin/mysql < "$PATH_SRC/dump/magento.sql"
}

apply_config(){
    cd "$PROJECT_PATH"
    bin/magento app:config:import
    bin/setup-domain magento.test
    bin/magento setup:upgrade
    bin/magento setup:di:compile
    bin/magento s:s:d -f
    bin/magento c:f
}

restart_project(){
    cd "$PROJECT_PATH"
    bin/restart
}

project_install(){
    echo  -e "\nInstall project" 2>/dev/null
    stop_services
    create_folder_install
    markshust_install_docker
    clone_repo
    db_prepare
    markshust_preimport_db
    markshust_db_import
    apply_config
    restart_project

    echo "==> user: admin " 2>/dev/null
    echo "==> password tiendamia2023" 2>/dev/null

    open https://magento.test
    open https://magento.test/fusion-backpack.html


    echo "============================== " 2>/dev/null
    echo "============================== " 2>/dev/null
    echo "============================== " 2>/dev/null
    echo "== PROXIMAMENTE EFDE PROJECT   " 2>/dev/null
    echo "============================== " 2>/dev/null
    open https://github.com/mmaximo33/EFDE#easy-and-fast-development-environment-efde-
}

requirements_validate
project_install
