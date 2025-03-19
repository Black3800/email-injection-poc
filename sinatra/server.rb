require 'sinatra'
require 'sinatra/flash'
require 'sinatra/redirect_with_flash'
require 'mail'
require 'json'
require 'securerandom'

set :bind, '0.0.0.0'  # Ensures it listens on all interfaces

# Replace these with your Mailgun credentials
SMTP_SERVER = 'smtp.eu.mailgun.org'
SMTP_PORT = 587
SMTP_USER = 'postmaster@mail.anakint.com'
SMTP_PASSWORD = '----'

# Your Mailgun domain
DOMAIN = 'mail.anakint.com'

# Configure Mail gem to use Mailgun SMTP server
Mail.defaults do
  delivery_method :smtp, {
    address: SMTP_SERVER,
    port: SMTP_PORT,
    user_name: SMTP_USER,
    password: SMTP_PASSWORD,
    authentication: 'plain',
    enable_starttls_auto: true
  }
end

enable :sessions
set :session_secret, SecureRandom.hex(64)

users = {}
pending_users = {}

get '/' do
  @welcome_message = session[:user] ? "Welcome, #{session[:user][:email]}!" : "Welcome to the Mailer App!"
  erb :index
end

get '/login' do
  erb :login
end

post '/login' do
  email = params[:email]
  password = params[:password]
  
  if users.key?(email) && users[email][:password] == password
    session[:user] = { email: email }
    redirect '/'
  else
    flash[:error] = "Invalid email or password."
    redirect '/login'
  end
end

get '/register' do
  erb :register
end

post '/register' do
  email = params[:email]
  password = params[:password]
  password_confirm = params[:password_confirm]
  
  if email.nil? || !email.end_with?("@#{DOMAIN}")
    flash[:error] = "Registration is restricted to @#{DOMAIN} email addresses."
    redirect '/register'
  end
  
  if password != password_confirm
    flash[:error] = "Passwords do not match."
    redirect '/register'
  end
  
  if users.key?(email) || pending_users.key?(email)
    flash[:error] = "Email already registered or pending confirmation."
    redirect '/register'
  else
    token = SecureRandom.hex(16)
    pending_users[email] = { password: password, token: token }
    
    Mail.deliver do
      to email
      from "no-reply@#{DOMAIN}"
      subject "Confirm your email"
      body "Click the link to confirm your email: http://localhost:4567/confirm/#{token}"
    end
    
    flash[:success] = "Registration successful. Check your email for confirmation."
    redirect '/login'
  end
end

get '/confirm/:token' do
  token = params[:token]
  user_entry = pending_users.find { |_, v| v[:token] == token }
  
  if user_entry
    email, data = user_entry
    users[email] = { password: data[:password] }
    pending_users.delete(email)
    flash[:success] = "Email confirmed. You can now log in."
  else
    flash[:error] = "Invalid or expired confirmation link."
  end
  
  redirect '/login'
end

get '/logout' do
  session.clear
  redirect '/'
end