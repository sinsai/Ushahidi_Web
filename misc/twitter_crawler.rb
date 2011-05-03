#-*- coding: utf-8 -*-
require 'net/http'
require 'json'
require 'uri'
require 'sequel'
require 'date'

Sequel::Model.plugin(:schema)
Sequel.connect("mysql://osm:osmosm@dbmaster/ushahidi", :encoding => "utf8")

class Message < Sequel::Model(:message)
end

class Incident < Sequel::Model(:incident)
end

class User < Sequel::Model(:users)
end

class Reporter < Sequel::Model(:reporter)
  many_to_one :level
  many_to_one :location
end

class Category < Sequel::Model(:category)
end

class IncidentCategory < Sequel::Model(:incident_category)
end

class Location < Sequel::Model(:location)
end

class Level < Sequel::Model(:level)
end

def expand_url(url)
  uri = url.kind_of?(URI) ? url : URI.parse(url)
  Net::HTTP.start(uri.host, uri.port) do |io|
    path = uri.path.empty? ? "/" : uri.path
    r = io.head(path)
    r['Location'] || uri.to_s
  end
end

def expand_url_string(text)
  text.gsub(/https?(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)/) do |url|
    expand_url(url)
  end
end

def put_to_db(tweet)
  # reporterがすでにいるか？いなかったら生成
  reporter = Reporter.find_or_create(
               :service_id=>3, # twitter
               :service_userid=>nil,
               :service_account=>tweet['user']['id'],
               :reporter_last=>nil,
               :reporter_email=>nil,
               :reporter_phone=>nil,
               :reporter_ip=>nil,
             )
  # find*の結果に対してsetをするとUPDATEクエリーに'LIMIT 1'が含まれる問題に対応。 #430
  reporters = Reporter.filter(:id=>reporter.id)
  reporters.set(
    :reporter_first=>tweet['user']['screen_name'],
    :reporter_date=>Time.now # datetime
  )
  reporters.set(:level_id=>Level.find(:level_weight => 0).id) unless reporter.level_id
  # reporterにもlevel_idを反映させるため
  reporter = reporters.first
  tweet_date = Time.parse(tweet['created_at'])
  if reporter.level_id > 1
    type = case tweet['text']+tweet['text_raw']
           when /RT/, /via/, /QT/
             1
           when /live at/, /ustre\.?am/, /#nhk/, /nico\.ms/, /lv\d+/, /nicovideo/
             2
           when /GIZMODO/i, /GIGAZINE/i, /NEWS4VIP/i, /APPBANK/i, /MAINICHI/i, /HEADLINES\.YAHOO/i
             3
           when /拡散希望/, /コピペ/, /followme/
             4
           else; 0
           end
    # type 5 alredy in use
    message = Message.insert(
                :parent_id=>0,
                :incident_id=>0,
                :user_id=>0,
                :reporter_id=>reporter.id, # created reporter
                :message_from=>tweet['user']['id'],
                :message_to=>nil,
                :message=>tweet['text'], # !new! for debug.
                :message_type=>1,
                :message_date=>tweet_date, # DATETIME
                :service_messageid=>tweet['id'],
                :type => type
              )
  end
  if reporter.location && reporter.level.level_weight > 0
    incident = Incident.insert(
                 :location_id=>reporter.location.id, # nanikore
                 :incident_title=>tweet['text'], #TODO string 50len
                 :incident_description=>tweet['text'],
                 :incident_date=>tweet_date,
                 :incident_dateadd=>Time.now(),
                 :incident_active=>1,
                 # if reporter.level.level_weight==2
                 # :incident_verified=>1,
               )
    message.update(:incident_id => incident.id)

    category = Category.filter(:category_trusted=>1)
    if category
      incident_category = IncidentCategory.insert(
                            :incident_id=>incident.id,
                            :category_id=>category.id
                          )
    end
  end
end

hashtags = %w(#jishin #j_j_helpme #hinan #anpi #311care)

uri = URI.parse('http://stream.twitter.com/1/statuses/filter.json')

abort "user pass" if ARGV.size < 2
user, pass = ARGV[0..1]

begin
  Net::HTTP.start(uri.host,uri.port) do |h|
    q = Net::HTTP::Post.new(uri.request_uri)
    q.basic_auth(user,pass)
    h.request(q,'track='+hashtags.map{|x|URI.escape(x)}.join(",")) do |r|
      buf = []
      r.read_body do |b|
        buf << b.chomp unless b.chomp.empty?
        j = JSON.parse(buf.join) rescue next
        buf = []
        if j["text"] && j["user"] && j["user"]["screen_name"]
          next if j["retweeted_status"] #j["text"].match(/RT /) || j["text"].match(/QT /)
          j["text_raw"] = j["text"].dup
          j["text"] = expand_url_string(j["text"])
          puts "#{j["user"]["screen_name"]}: #{j["text"]}"
          put_to_db j
        end
      end
    end
  end
rescue Interrupt
  exit
rescue Exception
  warn "#{$!.class}: #{$!.message}"
  warn $!.backtrace.map{|x| "\t#{x}"}.join("\n")
  retry
end
