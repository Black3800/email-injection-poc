# Use official Ruby image
FROM ruby:3.2

# Set working directory
WORKDIR /app

# Copy Gemfile and Gemfile.lock to leverage Docker cache
COPY ./sinatra/Gemfile ./sinatra/Gemfile.lock ./

# Install dependencies
RUN gem install bundler && bundle install

# Copy the application code
COPY ./sinatra/public/ ./public/
COPY ./sinatra/views/ ./views/
COPY ./sinatra/server.rb .

# Expose the Sinatra server port
EXPOSE 4567

# Run the web server
CMD ["ruby", "server.rb"]
