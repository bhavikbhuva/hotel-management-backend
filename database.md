
// ══════════════════════════════════════════════════════════════
// MIGRATED — these tables exist in the database right now
// ══════════════════════════════════════════════════════════════

Table settings {
  id int [pk]
  key varchar [unique]
  value text [null]
  created_at datetime
  updated_at datetime
}

Table countries {
  id int [pk]
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
  id int [pk]
  country_id int [unique, ref: > countries.id]
  created_at datetime
  updated_at datetime
}

Table users {
  id int [pk]
  branch_id int [null, ref: > branches.id]
  name varchar
  avatar varchar [null]
  email varchar [unique]
  phone varchar [null]
  password varchar
  role varchar [default: 'customer']
  status varchar [default: 'active']
  locale varchar [default: 'en']
  auth_provider varchar [null]
  country_id int [null, ref: > countries.id]
  referral_code varchar [null, unique]
  referred_by int [null, ref: > users.id]
  email_verified_at datetime [null]
  phone_verified_at datetime [null]
  last_login_at datetime [null]
  remember_token varchar [null]
  created_at datetime
  updated_at datetime
  deleted_at datetime [null]
}

Table property_types {
  id int [pk]
  name varchar [unique]
  description text [null]
  icon varchar [null]
  is_default boolean [default: false]
  is_active boolean [default: false]
  created_at datetime
  updated_at datetime
}

// ══════════════════════════════════════════════════════════════
// PLANNED — these tables are designed but NOT yet migrated
// ══════════════════════════════════════════════════════════════

Table cities {
  id int [pk]
  country_id int [ref: > countries.id]
  name varchar
  created_at datetime
}

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
