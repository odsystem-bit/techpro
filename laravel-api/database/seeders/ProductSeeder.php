<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Ebooks',
                'slug'        => 'ebooks',
                'description' => 'Livres numériques sur le business, la tech et le développement personnel.',
            ],
            [
                'name'        => 'Templates',
                'slug'        => 'templates',
                'description' => 'Modèles prêts à l\'emploi pour sites web, CV, présentations.',
            ],
            [
                'name'        => 'Formations',
                'slug'        => 'formations',
                'description' => 'Formations vidéo et guides pratiques pour monter en compétences.',
            ],
        ];

        foreach ($categories as $catData) {
            $category = Category::firstOrCreate(['slug' => $catData['slug']], $catData);
        }

        $ebooks    = Category::where('slug', 'ebooks')->first();
        $templates = Category::where('slug', 'templates')->first();
        $formations = Category::where('slug', 'formations')->first();

        $products = [
            [
                'name'              => 'Guide Ultime du Freelance en Afrique',
                'short_description' => 'Tout ce qu\'il faut savoir pour se lancer et réussir en freelance depuis l\'Afrique.',
                'description'       => 'Ce guide complet de 120 pages couvre la prospection client, la facturation, les plateformes de paiement adaptées à l\'Afrique, et les stratégies pour décrocher vos premiers contrats internationaux.',
                'price'             => 9500,
                'discount_price'    => 6500,
                'currency'          => 'XOF',
                'is_featured'       => true,
                'category_id'       => $ebooks->id,
                'features'          => ['120 pages', 'Exemples concrets', 'Modèles de contrats inclus', 'Accès à vie'],
            ],
            [
                'name'              => 'Dropshipping Academy',
                'short_description' => 'La méthode complète pour lancer votre boutique en ligne sans stock.',
                'description'       => 'Apprenez à sélectionner des produits gagnants, trouver des fournisseurs fiables et générer vos premières ventes en moins de 30 jours.',
                'price'             => 15000,
                'discount_price'    => null,
                'currency'          => 'XOF',
                'is_featured'       => false,
                'category_id'       => $ebooks->id,
                'features'          => ['85 pages', 'Liste de fournisseurs vérifiés', 'Scripts de négociation', 'Mises à jour gratuites'],
            ],
            [
                'name'              => 'Template CV Premium Tech',
                'short_description' => 'Démarquez-vous avec un CV moderne et professionnel pour les métiers du digital.',
                'description'       => 'Pack de 5 templates CV (Word + PDF) conçus pour les développeurs, designers et chefs de projet. Compatible ATS.',
                'price'             => 3500,
                'discount_price'    => 2500,
                'currency'          => 'XOF',
                'is_featured'       => true,
                'category_id'       => $templates->id,
                'features'          => ['5 designs inclus', 'Format Word + PDF', 'Compatible ATS', 'Police et couleurs personnalisables'],
            ],
            [
                'name'              => 'Kit Présentation Business Plan',
                'short_description' => 'Modèle PowerPoint professionnel pour convaincre investisseurs et banques.',
                'description'       => 'Template PowerPoint de 40 slides structuré selon les standards des investisseurs. Inclut les sections : résumé exécutif, analyse de marché, projections financières.',
                'price'             => 5000,
                'discount_price'    => null,
                'currency'          => 'XOF',
                'is_featured'       => false,
                'category_id'       => $templates->id,
                'features'          => ['40 slides prêts', 'Format PowerPoint + Keynote', 'Graphiques et tableaux inclus', 'Guide d\'utilisation'],
            ],
            [
                'name'              => 'Formation Développement Web Complet',
                'short_description' => 'De zéro à développeur full-stack en 60 heures de contenu vidéo.',
                'description'       => 'Formation complète HTML, CSS, JavaScript, PHP et Laravel. Accès aux vidéos, exercices corrigés et projets pratiques pour construire votre portfolio.',
                'price'             => 45000,
                'discount_price'    => 29900,
                'currency'          => 'XOF',
                'is_featured'       => true,
                'category_id'       => $formations->id,
                'features'          => ['60h de vidéo', 'Projets pratiques', 'Certificat de complétion', 'Accès à vie + mises à jour'],
            ],
            [
                'name'              => 'Masterclass Marketing Digital Afrique',
                'short_description' => 'Stratégies de marketing digital adaptées aux marchés africains.',
                'description'       => 'Apprenez à créer des campagnes Facebook/Instagram rentables, maîtriser le SEO local et développer votre présence sur WhatsApp Business pour le marché africain.',
                'price'             => 25000,
                'discount_price'    => null,
                'currency'          => 'XOF',
                'is_featured'       => false,
                'category_id'       => $formations->id,
                'features'          => ['30h de contenu', 'Études de cas Afrique', 'Templates publicitaires', 'Communauté privée'],
            ],
        ];

        foreach ($products as $data) {
            $data['slug']      = Str::slug($data['name']);
            $data['is_active'] = true;
            $data['stock']     = -1;
            Product::firstOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
