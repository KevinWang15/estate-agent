<?php
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    private function clearTable($tableName)
    {
        $this->command->comment("Clearing $tableName");
        \DB::statement("delete from $tableName");
        \DB::statement("alter table $tableName AUTO_INCREMENT=1;");
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        /** @var \Faker\Generator $faker */
        $faker = App::make(Faker\Generator::class);

        \DB::statement("SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;");

        $this->clearTable("buyers");
        $this->clearTable("sellers");
        $this->clearTable("estates");
        $this->clearTable("agents");
        $this->clearTable("users");
        $this->clearTable("agent_estate");
        $this->clearTable("orders");
        $this->clearTable("proposals");

        \DB::statement("SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;");

        $this->command->comment("Seeding users");
        factory(\App\User::class, 200)->create();

        $buyers = \App\User::where('user_type', 0)->get();
        $sellers = \App\User::where('user_type', 1)->get();
        $agents = \App\User::where('user_type', 2)->get();

        $this->command->comment("Seeding buyer");
        foreach ($buyers as $buyer) {
            /** @var \App\Buyer $instance */
            $instance = new \App\Buyer;
            $instance->user_id = $buyer->id;
            $instance->save();
        }

        $this->command->comment("Seeding agent");
        foreach ($agents as $agent) {
            /** @var \App\Agent $instance */
            $instance = new \App\Agent;
            $instance->user_id = $agent->id;
            $instance->title = $faker->company;
            $instance->fee = $faker->numberBetween(10000, 200000) / 100;
            $instance->description = $faker->paragraph;
            $instance->save();
        }

        $this->command->comment("Seeding seller");

        $is_verified = 1 - intval($faker->numberBetween(0, 7) / 5);

        foreach ($sellers as $seller) {
            /** @var \App\Seller $instance */
            $instance = new \App\Seller;
            $instance->user_id = $seller->id;
            $instance->verified = $is_verified;
            $instance->verified_by_agent_id = $is_verified ? App\Helper\Util::randomArrayMember($agents)->id : null;
            $instance->id_card_num = strval($faker->randomNumber(8)) . strval($faker->randomNumber(8));
            $instance->save();
        }


        $this->command->comment("Seeding estates");
        factory(\App\Estate::class, 500)->create();

        $this->command->comment("Mocking estate_agent relations");
        $estates_id = \App\Estate::where('verified', 1)->pluck("id");
        $agents_array = $agents->toArray();
        foreach ($estates_id as $estate_id) {
            shuffle($agents_array);
            $builder = [];
            for ($i = 0; $i < 5; $i++) {
                $builder[] = "({$agents_array[$i]["id"]},$estate_id)";
            }
            \DB::insert("insert into agent_estate (agent_id,estate_id) values " . implode(',', $builder));
        }

        $this->command->comment("Mocking proposals");
        $proposals = [];
        for ($i = 0; $i < 400; $i++) {
            $proposal = new \App\Proposal;
            $proposal->estate_id = \App\Helper\Util::randomArrayMember($estates_id);
            $agents_id = \DB::select("select agent_id from agent_estate where estate_id=?", [$proposal->estate_id]);
            $proposal->agent_id = \App\Helper\Util::randomArrayMember($agents_id)->agent_id;
            $proposal->buyer_id = \App\Helper\Util::randomArrayMember($buyers)->id;
            $proposal->state = $faker->numberBetween(-3, 3);
            $proposal->save();
            $proposals[] = $proposal;
        }

        shuffle($proposals);
        $this->command->comment("Mocking orders");
        for ($i = 0; $i < 100; $i++) {
            $estate_instance = \App\Estate::find($proposals[$i]->estate_id);
            $estate_instance->is_hidden = true;
            $estate_instance->save();

            $order = new \App\Order;
            $order->proposal_id = $proposals[$i]->id;
            $order->state = $faker->numberBetween(0, 2);
            $order->estate_id = $proposals[$i]->estate_id;
            $order->buyer_id = $proposals[$i]->buyer_id;
            $order->seller_id = $estate_instance->user_id;
            $order->save();

            $proposals[$i]->state = 4;
            $proposals[$i]->order_id = $order->id;
            $proposals[$i]->save();
        }
        return;
    }
}