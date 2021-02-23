use sejm_orka_all;
UPDATE posiedzenia set `text_css` =   REGEXP_REPLACE(tekst, '(\<p\>)([[:alpha:][:space:]]+)(\:\<\/p\>)', '\<p class=\"speaker\"\>$2$3') where tekst REGEXP('(\<p\>)([[:alpha:][:space:]]+)(\:\<\/p\>)');
UPDATE posiedzenia set `text_css` = `tekst` WHERE `text_css` = '';
-- UPDATE posiedzenia SET text_css = REGEXP_REPLACE(`text_css`, '([(]{1})([[:alnum:][:blank:][:punct:]]+)([)]{1})', '\<span class=\"asside\"\>$1$2$3\<\/span\>') where text_css REGEXP('([(]{1})([[:alnum:][:blank:][:punct:]]+)([)]{1})')
