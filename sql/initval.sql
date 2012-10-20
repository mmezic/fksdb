USE fksdb;

INSERT INTO country (country_iso, name_cs, name_en) VALUES
('CZ', 'Česká republika', 'Czech republic'),
('SK', 'Slovensko', 'Slovakia');

INSERT INTO region (country_iso, nuts, name) VALUES
('SK', 'SK010', 'Bratislavský kraj'),
('SK', 'SK021', 'Trnavský kraj'),
('SK', 'SK022', 'Trenčiansky kraj'),
('SK', 'SK023', 'Nitriansky kraj'),
('SK', 'SK031', 'Žilinský kraj'),
('SK', 'SK032', 'Banskobystrický kraj'),
('SK', 'SK041', 'Prešovský kraj'),
('SK', 'SK042', 'Košický kraj'),
('CZ', 'CZ010', 'Hlavní město Praha'),
('CZ', 'CZ020', 'Středočeský kraj'),
('CZ', 'CZ031', 'Jihočeský kraj'),
('CZ', 'CZ032', 'Plzeňský kraj'),
('CZ', 'CZ041', 'Karlovarský kraj'),
('CZ', 'CZ042', 'Ústecký kraj'),
('CZ', 'CZ051', 'Liberecký kraj'),
('CZ', 'CZ052', 'Královéhradecký kraj'),
('CZ', 'CZ053', 'Pardubický kraj'),
('CZ', 'CZ063', 'Kraj Vysočina'),
('CZ', 'CZ064', 'Jihomoravský kraj'),
('CZ', 'CZ071', 'Olomoucký kraj'),
('CZ', 'CZ072', 'Zlínský kraj'),
('CZ', 'CZ080', 'Moravskoslezský kraj');

INSERT INTO contest (contest_id, name) VALUES 
(1, 'FYKOS'),
(2, 'VÝFUK');