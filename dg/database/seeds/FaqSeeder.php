<?php

use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faq = array(
            ['title' => 'What is the Alchemy Wings mission?', 'description' => '<p>Our mission is to make enjoying life easy, whenever and wherever you are - every moment is important. We inspire you to take advantage of life, as soon and as much as possible.</p>', 'category' => '1', 'created_at' => new DateTime],
            ['title' => 'How does Alchemy Wings do this?', 'description' => '<p>We put fantastic products in your hand – fast. We show you how to mix liquids and partner foods in creative ways. We’re here when you want us, and we help you have fun.</p>', 'category' => '1', 'created_at' => new DateTime],
            ['title' => 'Who does Alchemy Wings benefit?', 'description' => '<p>We’re a gateway to your city, and we support local communities. We partner with local stores run by local people, and we’re proud of this. We help them be successful, meet new customers, and compete with bigger corporations.</p><p>Our couriers are also local and we are proud to work with them – we call them ‘Alchemists’. We value service, speed, and responsibility.</p>', 'category' => '1', 'created_at' => new DateTime],
            ['title' => 'What’s the story behind Alchemy Wings?', 'description' => '<p>Our founder (Sam) started out at Diageo (Smirnoff). All his friends worked too hard and he wanted to help them enjoy life a bit more. At the same time, he wanted to help the small storeowners that he worked with on a day-to-day basis compete with the big boys.</p><p>He decided to connect one with the other through a little bit of delivery magic, and Alchemy Wings was born…</p>', 'category' => '1', 'created_at' => new DateTime],
            
            //
            //
            ['title' => 'How does Alchemy Wings work?', 'description' => '<p>We keep it simple – enter your postcode and we’ll show you all the products available in your area. Choose which ones you want and we’ll collect these from the store and drop them off for you. You pay the store online through our portal.</p>', 'category' => '2', 'created_at' => new DateTime],
            ['title' => 'Which stores does Alchemy Wings work with?', 'description' => '<p>As many local stores as possible – we don’t charge a joining fee or monthly charge, and there’s no nasty conditions for the store. They only sell what they want, when they want to.</p>', 'category' => '2', 'created_at' => new DateTime],
            ['title' => 'What products does Alchemy Wings sell?', 'description' => '<p>All the best ones – we personally curate the list, and work with our partners to make sure you’re never disappointed. If you think we’re missing something let us know! We sell alcohol, tobacco, drinks, and snacks (mainly chocolate, sweets, and crisps).</p>', 'category' => '2', 'created_at' => new DateTime],
            ['title' => 'What times are you open / can I order?', 'description' => '<p>We want to be open all the time – unfortunately we’re only small, and we need to start somewhere. At the moment we’re available:</p>
							<p>Monday – Wednesday: 7am – 7pm<br>
							Thursday: 7am – midnight<br>
							Friday: 7am – 5am (next day)<br>
							Saturday: 11am – 5am (next day)<br>
							Sunday: 11am – midnight</p>', 'category' => '2', 'created_at' => new DateTime],
            ['title' => 'How are the products delivered to me?', 'description' => '<p>When you place an order we send this to our Alchemists, and they head to their local store to pick it up for you. Once the store owner has signed off the stock and agreed the sale your Alchemist will magic themselves across to you!</p>', 'category' => '2', 'created_at' => new DateTime],
            ['title' => 'Does Alchemy Wings accept cash?', 'description' => '<p>Absolutely not, for a few reasons – carrying large amounts of money makes it unsafe for our Alchemists, and small stores rely on quick and reliable cash flow (online payments mean we get their money back to them as fast as possible). Our Alchemists accept cash tips though!</p>', 'category' => '2', 'created_at' => new DateTime],
            ['title' => 'Should I tip my Alchemist?', 'description' => '<p>We’d love you to, but it’s completely up to you. Our Alchemists receive 100% of all tips, and they deserve as much support as possible for keeping the party going!</p>', 'category' => '2', 'created_at' => new DateTime],
            ['title' => 'Is there a minimum order value?', 'description' => '<p>No – this didn’t feel right. Sometimes you just want a packet of Maltesers. I absolutely love Maltesers. Give me Maltesers. So we don’t have a minimum spend, but we will charge you a small surcharge if your products are less than £10. This helps us keep our drivers on the road and our stores in business.</p>', 'category' => '2', 'created_at' => new DateTime],
            ['title' => 'How do I redeem a discount or promotional code?', 'description' => '<p>Where did you get that? Don’t tell anyone you cheeky devil! All codes can be redeemed through your personal profile, accessed at the top right of the screen.</p>', 'category' => '2', 'created_at' => new DateTime],
            ['title' => 'Do you charge the same prices as the store owner does in the store?', 'description' => '<p>We always try to charge a fair price for both the customer and the store owner, and this is fixed for delivery. All retailers selling on our platform agree to the sale at this price, and there’s no penalty if they don’t want to. Not all stores sell all products for delivery.</p>
							<p>Of course it’s completely the store owner’s decision what price they sell at in their store, and we leave this up to them.</p>', 'category' => '2', 'created_at' => new DateTime],
            ['title' => 'Can I place my order in advance or collect my order?', 'description' => '<p>Not yet, but this is a great idea and something we’re working on!</p>', 'category' => '2', 'created_at' => new DateTime],
            ['title' => 'How is the order packaged?', 'description' => '<p>This all depends on what you’ve ordered, whether alcohol, tobacco, drinks, or snacks. Our stores pride themselves on using packaging that’s safe, and we help them with this wherever possible.</p>', 'category' => '2', 'created_at' => new DateTime],
            
            //
            ['title' => 'What if something is wrong with my order?', 'description' => '<p>We have a dedicated customer service team whose focus is to ensure you have the best possible experience. Sometimes though things go wrong. In which case please contact us at <a style="font-weight:bold;" href="mailto:customerservice@alchemywings.co;">customerservice@alchemywings.co</a> and we’ll get back to you as soon as possible.</p>', 'category' => '3', 'created_at' => new DateTime],
            ['title' => 'What if I forget something on the order?', 'description' => '<p>Please contact our customer service team at <a style="font-weight:bold;" href="mailto:customerservice@alchemywings.co;">customerservice@alchemywings.co</a> and we’ll do our best to make sure you get all the items you want added to your order.</p>', 'category' => '3', 'created_at' => new DateTime],
            ['title' => 'What if my order is late?', 'description' => '<p>Our Alchemists are famous for acting at lightning speed, but sometimes delays happen for reasons that we can’t control. We’ll always try and let you know when this is the case, and update you on when you can expect your order. If you feel our service has ever let you down, please just let us know at <a style="font-weight:bold;" href="mailto:customerservice@alchemywings.co;">customerservice@alchemywings.co</a> and we’ll do our best to resolve this for you.</p>', 'category' => '3', 'created_at' => new DateTime],
            ['title' => 'What if I’m not around when my Alchemist arrives?', 'description' => '<p>If you think you may miss your Alchemist, or not be at your delivery address at the time of your delivery, please let us know by emailing <a style="font-weight:bold;" href="mailto:customerservice@alchemywings.co;">customerservice@alchemywings.co</a>. We will always try to call you if there is an issue, and if our driver struggles to reach you our customer service team will also attempt contact on their behalf.</p>
							<p>If we fail to reach you and are unable to deliver the order your Alchemist will wait for up to 10 minutes before leaving the area to return the product. In this event we are sorry to confirm that you will still be charged for your order. To avoid this we ask that you always check your contact details and delivery address are up-to-date on the platform.</p>', 'category' => '3', 'created_at' => new DateTime],
            
            //
            ['title' => 'What if I have allergies or an allergic reaction?', 'description' => '<p>We do our best to include all allergy information on the product under Product Specifications, and in the Product Description. This information may not always be accurate, and you should always check the product packaging and contact the manufacturer before you consume if you’re at all unsure.</p>', 'category' => '4', 'created_at' => new DateTime],
            ['title' => 'When will you deliver to my area?', 'description' => '<p>We’re very new and even more keen to expand. We rely on our customers to tell us the next place to launch, and your feedback to <a style="font-weight:bold;" href="mailto:customerservice@alchemywings.co;">customerservice@alchemywings.co</a> is really appreciated. Please keep checking back as we plan to launch new areas as frequently as possible. Let your local store know if you want us in your area, as they’re the superstars that help us expand our business!</p>', 'category' => '4', 'created_at' => new DateTime],
            ['title' => 'Does Alchemy Wings have an App?', 'description' => '<p>We hope to launch our iOS and Android Apps at the end of July, so please keep checking back and we’ll let you know as soon as these are available. Until then we hope our website provides everything you need. Just let us know if you have any suggestions!</p>', 'category' => '4', 'created_at' => new DateTime],
            
            //
           
        );
        DB::table('faqs')->insert($faq);
    }
}
