require "capistrano_colors"

load "app/config/capifony/parameters.rb"

set :application, "Bisouland"

set(:environment) { Capistrano::CLI.ui.ask("Which environment: preprod or prod?") }
load "app/config/capifony/config_#{environment}.rb"

set :scm, :git
set :repository, "git://github.com/pyricau/bisouland.git"
set :branch, "master"

set :deploy_via, :remote_cache

role :web, domain
role :app, domain
role :db, domain, :primary => true

set :use_sudo, false

set :use_composer, true
set :dump_assetic_assets, true
set :interactive_mode, false

set :shared_files, [ app_path + "/config/parameters.yml" ]
set :shared_children, [ log_path, "vendor", app_path + "/sessions" ]

set :writable_dirs, [ log_path, cache_path, app_path + "/sessions" ]
set :permission_method, :acl
set :use_set_permissions, true

set  :keep_releases,  3

after "deploy", "deploy:cleanup"
