INSERT INTO schedule_item (schedule_item_id,capacity, name_cs,name_en, price_czk,price_eur,schedule_group_id)
SELECT event_accommodation_id,capacity, name,name, price_kc,price_eur,1 FROM `event_accommodation`;

INSERT INTO person_schedule (person_schedule_id,person_id, schedule_item_id,state)
SELECT event_person_accommodation_id,person_id, event_accommodation_id,status FROM `event_person_accommodation`;
