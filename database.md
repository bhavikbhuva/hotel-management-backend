
// ══════════════════════════════════════════════════════════════
// REFERENCE TABLES — read-only data pool from dr5hn SQL files
// Imported via: php artisan app:import-ref-data (mysql CLI pipe)
// NOT managed by Laravel migrations
// ══════════════════════════════════════════════════════════════

Table ref_countries {
  id mediumint unsigned [pk, auto]
  name varchar(100)
  iso3 char(3) [null]
  numeric_code char(3) [null]
  iso2 char(2) [null]
  phonecode varchar [null]
  capital varchar [null]
  currency varchar [null]
  currency_name varchar [null]
  currency_symbol varchar [null]
  tld varchar [null]
  native varchar [null]
  population bigint unsigned [null]
  gdp bigint unsigned [null]
  region varchar [null]
  region_id mediumint unsigned [null]
  subregion varchar [null]
  subregion_id mediumint unsigned [null]
  nationality varchar [null]
  area_sq_km double [null]
  postal_code_format varchar [null]
  postal_code_regex varchar [null]
  timezones text [null]
  translations text [null]
  latitude decimal(10,8) [null]
  longitude decimal(11,8) [null]
  emoji varchar [null]
  emojiU varchar [null]
  created_at timestamp [null]
  updated_at timestamp [default: `CURRENT_TIMESTAMP`]
  flag boolean [default: 1]
  wikiDataId varchar [null]

  indexes {
    (region_id) [name: 'country_continent']
    (subregion_id) [name: 'country_subregion']
  }
}

Table ref_states {
  id mediumint unsigned [pk, auto]
  name varchar
  country_id mediumint unsigned [ref: > ref_countries.id]
  country_code char(2)
  fips_code varchar [null]
  iso2 varchar [null]
  iso3166_2 varchar(10) [null]
  type varchar(191) [null]
  level int [null]
  parent_id int unsigned [null]
  native varchar [null]
  latitude decimal(10,8) [null]
  longitude decimal(11,8) [null]
  timezone varchar [null]
  translations text [null]
  created_at timestamp [null]
  updated_at timestamp [default: `CURRENT_TIMESTAMP`]
  flag boolean [default: 1]
  wikiDataId varchar [null]
  population varchar [null]

  indexes {
    (country_id) [name: 'country_region']
  }
}

Table ref_cities {
  id mediumint unsigned [pk, auto]
  name varchar
  state_id mediumint unsigned [ref: > ref_states.id]
  state_code varchar
  country_id mediumint unsigned [ref: > ref_countries.id]
  country_code char(2)
  type varchar(191) [null]
  level int [null]
  parent_id int unsigned [null]
  latitude decimal(10,8)
  longitude decimal(11,8)
  native varchar [null]
  population bigint unsigned [null]
  timezone varchar [null]
  translations text [null]
  created_at timestamp [default: '2014-01-01 12:01:01']
  updated_at timestamp [default: `CURRENT_TIMESTAMP`]
  flag boolean [default: 1]
  wikiDataId varchar [null]

  indexes {
    (state_id) [name: 'ref_cities_test_ibfk_1']
    (country_id) [name: 'ref_cities_test_ibfk_2']
  }
}

// ══════════════════════════════════════════════════════════════
// MIGRATED — these tables exist in the database right now
// Managed by Laravel migrations
// ══════════════════════════════════════════════════════════════

Table settings {
  id int [pk]
  key varchar [unique]
  value text [null]
  created_at datetime
  updated_at datetime
}

Table countries {
  id bigint unsigned [pk, auto]
  ref_country_id mediumint unsigned [null, unique, note: 'Links to ref_countries.id, no FK constraint']
  name varchar
  iso_code char(2) [unique]
  currency_symbol varchar [null]
  currency_code char(3)
  currency_name varchar
  is_active boolean [default: true]
  created_at datetime
  updated_at datetime
}

Table operating_countries {
  id bigint unsigned [pk, auto]
  country_id bigint unsigned [unique, ref: > countries.id, note: 'ON DELETE CASCADE']
  created_at datetime
  updated_at datetime
}

Table states {
  id bigint unsigned [pk, auto]
  ref_state_id mediumint unsigned [null, unique, note: 'Links to ref_states.id, no FK constraint']
  country_id bigint unsigned [ref: > countries.id, note: 'ON DELETE CASCADE']
  name varchar
  latitude decimal(10,8) [null]
  longitude decimal(11,8) [null]
  is_active boolean [default: true]
  created_at datetime
  updated_at datetime
  deleted_at datetime [null]
}

Table cities {
  id bigint unsigned [pk, auto]
  ref_city_id mediumint unsigned [null, unique, note: 'Links to ref_cities.id, no FK constraint']
  country_id bigint unsigned [ref: > countries.id, note: 'ON DELETE CASCADE']
  state_id bigint unsigned [ref: > states.id, note: 'ON DELETE CASCADE']
  name varchar
  latitude decimal(10,8) [null]
  longitude decimal(11,8) [null]
  status varchar [default: 'active']
  created_at datetime
  updated_at datetime
  deleted_at datetime [null]

  indexes {
    (status) [name: 'cities_status_index']
  }
}

Table users {
  id bigint unsigned [pk, auto]
  branch_id bigint unsigned [null, ref: > branches.id]
  name varchar
  avatar varchar [null]
  email varchar [unique]
  phone varchar [null]
  password varchar
  role varchar [default: 'customer']
  status varchar [default: 'active']
  locale varchar [default: 'en']
  auth_provider varchar [null]
  country_id bigint unsigned [null, ref: > countries.id]
  current_country_id bigint unsigned [null, ref: > countries.id]
  current_branch_id bigint unsigned [null, ref: > branches.id]
  referral_code varchar [null, unique]
  referred_by bigint unsigned [null, ref: > users.id]
  email_verified_at datetime [null]
  phone_verified_at datetime [null]
  last_login_at datetime [null]
  remember_token varchar [null]
  created_at datetime
  updated_at datetime
  deleted_at datetime [null]
}

Table property_types {
  id bigint unsigned [pk, auto]
  name varchar [unique]
  description text [null]
  icon varchar [null]
  is_default boolean [default: false]
  is_active boolean [default: false]
  created_at datetime
  updated_at datetime
}

Table country_setup_tasks {
  id bigint unsigned [pk, auto]
  country_id bigint unsigned [null, ref: > countries.id]
  task_key varchar
  completed_at datetime [null]
  created_at datetime
  updated_at datetime

  indexes {
    (country_id, task_key) [unique]
  }
}

Table taxes {
  id bigint unsigned [pk, auto]
  country_id bigint unsigned [ref: > countries.id]
  property_type_id bigint unsigned [ref: > property_types.id]
  name varchar
  description text [null]
  type varchar
  value decimal(10,4)
  status varchar [default: 'active']
  created_at datetime
  updated_at datetime
  deleted_at datetime [null]
}

Table blog_categories {
  id bigint unsigned [pk, auto]
  name varchar
  slug varchar [unique]
  status varchar [default: 'draft', note: 'Enum: draft, published']
  created_at datetime
  updated_at datetime
  deleted_at datetime [null]

  indexes {
    (status) [name: 'blog_categories_status_index']
  }
}

Table blogs {
  id bigint unsigned [pk, auto]
  blog_category_id bigint unsigned [ref: > blog_categories.id, note: 'ON DELETE CASCADE']
  created_by bigint unsigned [null, ref: > users.id, note: 'ON DELETE SET NULL']
  title varchar
  slug varchar [unique]
  short_description text
  content longtext
  cover_image varchar
  meta_title varchar
  meta_description text
  keywords text
  status varchar [default: 'draft', note: 'Enum: draft, published']
  published_at datetime [null]
  created_at datetime
  updated_at datetime
  deleted_at datetime [null]

  indexes {
    (status) [name: 'blogs_status_index']
    (blog_category_id) [name: 'blogs_blog_category_id_foreign']
    (created_by) [name: 'blogs_created_by_foreign']
  }
}

Table banners {
  id bigint unsigned [pk, auto]
  country_id bigint unsigned [null, ref: > countries.id, note: 'ON DELETE CASCADE, nullable for future global banners']
  is_global boolean [default: false, note: 'Future: true = visible across all countries']
  platform varchar [default: 'both', note: 'Future: app, web, both']
  title varchar
  image varchar
  target_url varchar
  start_date date [null, note: 'Optional scheduling start']
  end_date date [null, note: 'Optional scheduling end']
  status varchar [default: 'active', note: 'Enum: active, inactive']
  sort_order int unsigned [default: 0, note: 'Future: display ordering']
  created_at datetime
  updated_at datetime
  deleted_at datetime [null]

  indexes {
    (status) [name: 'banners_status_index']
    (is_global) [name: 'banners_is_global_index']
  }
}

Table how_it_works_steps {
  id bigint unsigned [pk, auto]
  title varchar
  description varchar [note: 'Max 90 chars']
  sort_order int unsigned [default: 0, note: 'Auto-assigned, re-sequenced on delete']
  created_at datetime
  updated_at datetime
  deleted_at datetime [null]
}

Table faq_topics {
  id bigint unsigned [pk, auto]
  title varchar
  slug varchar [unique]
  description text
  sort_order int unsigned [default: 0, note: 'Auto-assigned, re-sequenced on delete']
  created_at datetime
  updated_at datetime
  deleted_at datetime [null]
}

Table faqs {
  id bigint unsigned [pk, auto]
  faq_topic_id bigint unsigned [ref: > faq_topics.id, note: 'ON DELETE CASCADE']
  question text
  answer text
  sort_order int unsigned [default: 0, note: 'Auto-assigned per topic, re-sequenced on delete']
  created_at datetime
  updated_at datetime
  deleted_at datetime [null]
}

// ══════════════════════════════════════════════════════════════
// MIGRATED (continued)
// ══════════════════════════════════════════════════════════════

Table homepage_sections {
  id bigint unsigned [pk, auto]
  section_key varchar [unique, note: 'e.g. about_us, amenities, guest_reviews']
  title varchar [null]
  description text [null]
  button_text varchar [null]
  contact_no varchar [null]
  image varchar [null, note: 'Stored in public/homepage/. PNG/SVG, max 2MB, 770x600px']
  is_active boolean [default: true]
  amenities_data json [null, note: 'Array of {facility_id, description}']
  reviews_data json [null, note: 'Array of review IDs']
  created_at datetime
  updated_at datetime
}

Table reviews {
  id bigint unsigned [pk, auto]
  booking_id bigint unsigned [unique]
  user_id bigint unsigned
  property_id bigint unsigned [null]
  rating decimal(2,1)
  review text
  status varchar [default: 'pending', note: 'enum: pending, approved, hidden']
  is_visible boolean [default: true]
  is_edited boolean [default: false]
  edited_at timestamp [null]
  removal_requested boolean [default: false]
  removal_status varchar [null, note: 'enum: pending, approved, rejected']
  approved_by bigint unsigned [null]
  approved_at timestamp [null]
  created_at timestamp
  updated_at timestamp
}

// ══════════════════════════════════════════════════════════════
// PLANNED — these tables are designed but NOT yet migrated
// ══════════════════════════════════════════════════════════════


Table currencies {
  id int [pk]
  code varchar
  symbol varchar
  name varchar
}

Table partners {
  id int [pk]
  user_id int [ref: > users.id]
  business_name varchar
  business_email varchar
  business_phone varchar
  country_id int [ref: > countries.id]
  city_id int [ref: > cities.id]
  address varchar
  logo varchar
  tax_number varchar
  commission_rate decimal
  status varchar
  kyc_verified_at datetime
  approved_at datetime
  created_at datetime
  updated_at datetime
  deleted_at datetime
}

Table properties {
  id int [pk]
  partner_id int [ref: > partners.id]
  property_type_id int [ref: > property_types.id]
  name varchar
  country_id int [ref: > countries.id]
  city_id int [ref: > cities.id]
  address varchar
  latitude decimal
  longitude decimal
  status varchar
  created_at datetime
  updated_at datetime
  deleted_at datetime
}

Table branches {
  id int [pk]
  property_id int [ref: > properties.id]
  name varchar
  country_id int [ref: > countries.id]
  city_id int [ref: > cities.id]
  address varchar
  latitude decimal
  longitude decimal
  checkin_time time
  checkout_time time
  currency_id int [ref: > currencies.id]
  status varchar
}

Table room_types {
  id int [pk]
  branch_id int [ref: > branches.id]
  name varchar
  description text
  max_guests int
  base_price decimal
  total_rooms int
  status varchar
  created_at datetime
  updated_at datetime
  deleted_at datetime
}

Table room_inventory {
  id int [pk]
  room_type_id int [ref: > room_types.id]
  date date
  total_rooms int
  booked_rooms int
  created_at datetime
  updated_at datetime

  indexes {
    (room_type_id, date) [unique]
  }
}

Table inventory_locks {
  id int [pk]
  user_id int [ref: > users.id]
  room_type_id int [ref: > room_types.id]
  check_in date
  check_out date
  quantity int
  expires_at datetime
  created_at datetime
}

Table bookings {
  id int [pk]
  user_id int [ref: > users.id]
  branch_id int [ref: > branches.id]
  room_type_id int [ref: > room_types.id]
  quantity int
  check_in date
  check_out date
  total_guests int
  currency_id int [ref: > currencies.id]
  total_amount decimal
  tax_amount decimal
  discount_amount decimal
  final_amount decimal
  status varchar
  created_at datetime
  updated_at datetime
}

Table payments {
  id int [pk]
  booking_id int [ref: > bookings.id]
  payment_gateway varchar
  transaction_id varchar
  amount decimal
  currency_id int [ref: > currencies.id]
  status varchar
  is_refunded boolean
  paid_at datetime
  created_at datetime
}
