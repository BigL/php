set :application, "php"
set :repository,  "git@github.com:BigL/#{application}.git"

# set :scm, :git # You can set :scm explicitly or Capistrano will make an intelligent guess based on known version control directory names
# Or: `accurev`, `bzr`, `cvs`, `darcs`, `git`, `mercurial`, `perforce`, `subversion` or `none`

role :web, "www.infinitumphp.co.za"                          # Your HTTP server, Apache/etc
set :home, "/home/infinitu/"
set :deploy_to,"#{home}public_html/html5"
set :branch, 'master'
default_run_options[:pty] = true
#role :app, "your app-server here"                          # This may be the same as your `Web` server
#role :db,  "your primary db-server here", :primary => true # This is where Rails migrations will run
#role :db,  "your slave db-server here"

set :scm,"git"
set :user,"infinitu"
#set :port,"41414"
#set :scm_passphrase, "10a0609f"
set :ssh_options,{:forward_agent => true}
set :use_sudo, false
# if you want to clean up old releases on each deploy uncomment this:
# after "deploy:restart", "deploy:cleanup"

# if you're still using the script/reaper helper you will need
# these http://github.com/rails/irs_process_scripts

# If you are using Passenger mod_rails uncomment this:
# namespace :deploy do
#   task :start do ; end
#   task :stop do ; end
#   task :restart, :roles => :app, :except => { :no_release => true } do
#     run "#{try_sudo} touch #{File.join(current_path,'tmp','restart.txt')}"
#   end
# end
