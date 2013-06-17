# Deployment

Deployment to the pre-production and production servers is automated.

First of all set the deployment configuration:

    cp app/config/capifony/parameters.rb{,.dist}
    nano app/config/capifony/parameters.rb

Then you can use the following commands:

* If it is not the first time you deploy: `cap deploy`;
* else:
  1. Set the server for deployment: `cap deploy:setup`;
  2. try a first deployment: `cap deploy:cold`;
  3. if it doesn't work, connect to the server and set the
     `app/configparameters.yml` file;
  4. then deploy: `cap deploy`.
