DELETE FROM wp_posts WHERE post_type = 'revision';
DELETE FROM wp_postmeta WHERE post_id NOT IN (SELECT ID from wp_posts);
DELETE FROM wp_yoast_seo_links WHERE post_id NOT IN (SELECT ID from wp_posts);
DELETE FROM wp_yoast_primary_term WHERE post_id NOT IN (SELECT ID from wp_posts);
DELETE FROM wp_yoast_indexable WHERE object_type = 'post' AND object_id NOT IN (SELECT ID from wp_posts);
DELETE FROM wp_wffilemods;
DELETE FROM wp_usermeta WHERE user_id NOT IN (SELECT ID FROM wp_users);
DELETE FROM wp_pmxi_hash WHERE post_id NOT IN (SELECT ID from wp_posts);
