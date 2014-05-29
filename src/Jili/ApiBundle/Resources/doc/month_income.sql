INSERT INTO `month_income` (`id`, `income`) VALUES
(100, '1000元以下'),
(101, '1000元-2000元'),
(102, '2000元-3000元'),
(103, '3000元-4000元'),
(104, '4000元-5000元'),
(105, '5000元-6000元'),
(106, '6000元-7000元'),
(107, '7000元-8000元'),
(108, '8000元-9000元'),
(109, '9000元-10000元'),
(110, '10010元以上');


UPDATE user SET income = '100' WHERE  income ='1';
UPDATE user SET income = '103' WHERE  income ='2';
UPDATE user SET income = '105' WHERE  income ='3';
UPDATE user SET income = '110' WHERE  income ='4';