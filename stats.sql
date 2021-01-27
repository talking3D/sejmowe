SELECT COUNT(*) FROM `sentyment`;
SELECT sentyment, count(sentyment) FROM `sentyment` GROUP BY sentyment;
SELECT DATE(created), COUNT(id) FROM `sentyment` GROUP BY DATE(created);