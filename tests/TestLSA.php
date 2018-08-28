<?php

require '../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use  PHPLsa\LSA;

/**
 * Class TestLSA
 */
class TestLSA extends TestCase
{
    /**
     * @return LSA
     */
    public function testInitLsa() {
        $keyWords = ['quick', 'brown', 'fox', 'jumped', 'lazy', 'dog', 'hey', 'diddle', 'cat',
                     'fiddle', 'cow',  'moon', 'little', 'laughed', 'fun',  'dish', 'ran',
                     'away', 'spoon',  ];
        //$keyWords = ['test', 'abc', 'mamaba', 'dog', 'kert', 'qwer', 'samba', 'cat', 'moreaxsxs'];
        $transformText = new \PHPLsa\TransformTextByKeyWord($keyWords);
        $trans = $transformText->transform(['ABC']);
        $this->assertTrue(count($keyWords) == count($trans));

        $lsa = new LSA(4);
        $lsa->setTextTransformer($transformText);

        return $lsa;
    }

    /**
     * @param LSA $lsa
     * @depends testInitLsa
     */
    public function testFit(LSA $lsa) {
        $documents = [
            "The quick brown fox jumped over the lazy dog",
            "hey diddle diddle, the cat and the fiddle",
            "the cow jumped over the moon",
            "the little dog laughed to see such fun",
            "and the dish ran away with the spoon",
            "hey diddle diddle"
        ];/*
        $documents = [
            "test abc mamaba dog",
            "kert mamaba qwer dog",
            "test samba cat abc",
            "moreaxsxs test",
            "mamaba qwer",
            "test abc",
        ];*/

        $trans = $lsa->fitTransform($documents);
        $query = "the brown fox ran around the dog";
        $qTrans = $lsa->transform([$query]);
        $wstr = ['w'=> -1, 'index'=> -1];

        //print_r($trans); exit();
        for($n = 0; $n < count($trans[0]); $n++) {
            $sum = 0.0;
            $sum1 = 0.0;
            $sum2 = 0.0;

            for($i = 0; $i < count($trans); $i++) {
                $sum += $trans[$i][$n] * $qTrans[$i][0];
                $sum1 += $trans[$i][$n] * $trans[$i][$n];
                $sum2 += $qTrans[$i][0] * $qTrans[$i][0];
            }

            $w = $sum / (sqrt($sum1  + 0.000001) * sqrt($sum2  + 0.000001) );
            echo $w, PHP_EOL;
            if($wstr['index'] == -1 || $w > $wstr['w']) {
                $wstr['index'] = $n;
                $wstr['w'] = $w;
            }
        }

        //var_dump($wstr['index']); exit();
        echo $documents[$wstr['index']], PHP_EOL;

        $this->assertTrue($wstr['index'] == 0);



    }


    public function testRu() {
        $documents = [
            'Двое солдат ВСУ погибли, семеро ранены. Армия ДНР опубликовала видеодоказательства нарушения перемирия Киевом. Экс-министр обороны Украины Анатолий Гриценко озвучил новый план по захвату Донбасса. Последние новости Новороссии — в обзоре ФАН.',
            'Министр иностранных дел Австрии Карин Кнайсль упала в обморок во время Европейского форума.',
            '«Большой друг и защитник»: в Киеве предложили переименовать улицу в честь сенатора Маккейна',
            'В Киеве может появиться улица имени американского сенатора Джона Маккейна. Петиция с таким предложением появилась на сайте Киевской городской администрации',
            'Президент Украины Петр Порошенко в сентябре примет участие в заседании Генеральной ассамблеи ООН. Об этом сообщил журналистам посол Украины в США Валерий Чалый.',
            'Официальный представитель президента России Владимира Путина Дмитрий Песков рассказал, как глава государства провел выходные. В понедельник российский лидер находится в Кемерово, где обсуждает вопросы развития ТЭК и экологической безопасности.',
            'Переброшенный в Средиземное море эсминец США USS Ross, вооруженный 28 ракетами «Томагавк», может нанести удар по любой части Сирии.',
            'Помощник генерального секретаря НАТО по политическим вопросам и политике безопасности Алехандро Альваргонсалес может приехать в Киев 13-14 сентября.',
            'Россия считает привязку новых санкций США к «делу Скрипаля» абсолютно надуманной, а сами обвинения Вашингтона в связи с инцидентом в Солсбери эфемерными. Об этом говорится в комментарии, опубликованном в понедельник на официальном сайте МИД РФ.',

            'В «Манчестер Юнайтед» зреет очередной конфликт – между главным тренером и лучшим игроком команды пробежала кошка.',
            '«Ростов» Валерия Карпина выиграл на старте сезона четыре матча из пяти, а желает гораздо большего.',
            'Главный тренер брестского «Динамо» Алексей Шпилевский отправлен в отставку. При этом, специалист не покидает клуб, а продолжит работать в «Динамо» в другой должности. Вероятно, работа 30-летнего специалиста будет теперь связана с динамовской футбольной академией.',

            ];


        $lsa = new LSA(4);
        $lsa->setTextTransformer( new \PHPLsa\TransformTextByKeyWord([
                'россия', 'санкций', 'вопрос', 'вопрос', 'петиция', 'политика', 'сша', 'ДНР', 'Двое', 'Солдат', 'путин',
                'Президент', 'удар', 'Армия', 'Украин', 'тренер', 'отставка', 'конфликт', 'игрок', 'Манчестер', 'Юнайтед',
                'упал', 'министр'
            ]));
        $trans = $lsa->fitTransform($documents);

        $this->assertTrue(!empty($trans));
        //print_r($matrix);

        $query = "министр упал США";
        $qTrans = $lsa->transform([$query]);
        $wstr = ['w'=> -1, 'index'=> -1];

        for($n = 0; $n < count($trans[0]); $n++) {
            $sum = 0.0;
            $sum1 = 0.0;
            $sum2 = 0.0;

            for($i = 0; $i < count($trans); $i++) {
                $sum += $trans[$i][$n] * $qTrans[$i][0];
                $sum1 += $trans[$i][$n] * $trans[$i][$n];
                $sum2 += $qTrans[$i][0] * $qTrans[$i][0];
            }

            $w = abs(  $sum / (sqrt($sum1  + 0.000001) * sqrt($sum2  + 0.000001) ));
            echo $w, PHP_EOL;
            if($wstr['index'] == -1 || $w > $wstr['w']) {
                $wstr['index'] = $n;
                $wstr['w'] = $w;
            }
        }

        print_r($wstr['w']);

      //  echo $documents[ count($documents) - 3];
        echo $documents[ $wstr['index']];

    }
}