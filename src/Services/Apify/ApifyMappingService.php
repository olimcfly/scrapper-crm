<?php

declare(strict_types=1);

namespace App\Services\Apify;

final class ApifyMappingService
{
    /**
     * @param array<int, array<string, mixed>> $items
     *
     * @return array<int, array<string, mixed>>
     */
    public function mapDataset(string $source, array $items): array
    {
        return array_map(fn (array $item): array => $this->mapItem($source, $item), $items);
    }

    /**
     * @param array<string, mixed> $item
     *
     * @return array<string, mixed>
     */
    public function mapItem(string $source, array $item): array
    {
        return match ($source) {
            'google_maps' => $this->mapGoogleMaps($item),
            'instagram' => $this->mapInstagramScraper($item),
            'instagram_profile' => $this->mapInstagramProfile($item),
            'instagram_hashtag' => $this->mapInstagramHashtag($item),
            'linkedin_profile' => $this->mapLinkedInProfile($item),
            'tiktok' => $this->mapTikTok($item),
            default => $this->mapGeneric($source, $item),
        };
    }

    /** @param array<string, mixed> $item */
    private function mapInstagramScraper(array $item): array
    {
        return [
            'prospect' => [
                'full_name' => $this->string($item['ownerFullName'] ?? $item['ownerUsername'] ?? ''),
                'business_name' => '',
                'activity' => 'instagram_posts',
                'city' => '',
                'country' => '',
                'website' => '',
                'instagram_url' => $this->string($item['url'] ?? ''),
            ],
            'social_profile' => [
                'platform' => 'instagram',
                'profile_url' => $this->string($item['ownerProfilePicUrl'] ?? ''),
                'username' => $this->string($item['ownerUsername'] ?? ''),
                'followers_count' => null,
                'engagement_rate' => null,
            ],
            'enrichment_data' => [
                'post_type' => $this->string($item['type'] ?? ''),
                'caption' => $this->string($item['caption'] ?? ''),
                'likes_count' => $item['likesCount'] ?? null,
                'comments_count' => $item['commentsCount'] ?? null,
            ],
            'analysis' => [
                'mvp_score' => $this->computeMvpScore($item),
                'next_action' => 'Analyser les contenus performants puis qualifier le prospect Instagram.',
                'summary' => 'Posts Instagram collectés via Apify et prêts pour enrichissement CRM.',
            ],
            'activity_log' => [
                'source' => 'apify/instagram',
                'event' => 'signal_collected',
                'details' => 'Posts Instagram normalisés depuis l’acteur apify/instagram-scraper.',
            ],
        ];
    }

    /** @param array<string, mixed> $item */
    private function mapGoogleMaps(array $item): array
    {
        $businessName = $this->string($item['title'] ?? $item['name'] ?? '');
        $phone = $this->string($item['phone'] ?? $item['phoneUnformatted'] ?? '');
        $website = $this->string($item['website'] ?? '');
        $address = $this->string($item['address'] ?? '');

        return [
            'prospect' => [
                'full_name' => $businessName,
                'business_name' => $businessName,
                'activity' => $this->string($item['categoryName'] ?? ''),
                'city' => $this->string($item['city'] ?? ''),
                'country' => $this->string($item['countryCode'] ?? ''),
                'website' => $website,
                'professional_phone' => $phone,
                'professional_email' => $this->extractEmailFromText($this->string($item['emails'] ?? '')),
            ],
            'social_profile' => [
                'platform' => 'google_maps',
                'profile_url' => $this->string($item['url'] ?? ''),
                'followers_count' => null,
                'engagement_rate' => null,
            ],
            'enrichment_data' => [
                'rating' => $item['totalScore'] ?? null,
                'reviews_count' => $item['reviewsCount'] ?? null,
                'categories' => $item['categories'] ?? [],
                'opening_hours' => $item['openingHours'] ?? [],
                'contact_phone' => $phone,
            ],
            'analysis' => [
                'mvp_score' => $this->computeMvpScore($item),
                'next_action' => 'Vérifier la présence sociale puis lancer un contact contextualisé.',
                'summary' => 'Prospect issu de Google Maps, prêt pour enrichissement ciblé.',
            ],
            'activity_log' => [
                'source' => 'apify/google_maps',
                'event' => 'prospect_collected',
                'details' => 'Collecte externe puis mapping métier Google Maps.',
            ],
            'google_business_location' => [
                'address' => $address,
                'postal_code' => $this->string($item['postalCode'] ?? ''),
                'state' => $this->string($item['state'] ?? ''),
                'latitude' => $item['location']['lat'] ?? null,
                'longitude' => $item['location']['lng'] ?? null,
            ],
        ];
    }

    /** @param array<string, mixed> $item */
    private function mapInstagramProfile(array $item): array
    {
        $fullName = $this->string($item['fullName'] ?? $item['username'] ?? '');

        return [
            'prospect' => [
                'full_name' => $fullName,
                'first_name' => $this->firstName($fullName),
                'last_name' => $this->lastName($fullName),
                'business_name' => $this->string($item['businessCategoryName'] ?? ''),
                'activity' => $this->string($item['businessCategoryName'] ?? ''),
                'city' => '',
                'country' => '',
                'website' => $this->string($item['externalUrl'] ?? ''),
                'instagram_url' => $this->string($item['url'] ?? ''),
            ],
            'social_profile' => [
                'platform' => 'instagram',
                'profile_url' => $this->string($item['url'] ?? ''),
                'username' => $this->string($item['username'] ?? ''),
                'followers_count' => $item['followersCount'] ?? null,
                'engagement_rate' => null,
            ],
            'enrichment_data' => [
                'biography' => $this->string($item['biography'] ?? ''),
                'is_business_account' => (bool) ($item['isBusinessAccount'] ?? false),
                'posts_count' => $item['postsCount'] ?? null,
                'profile_pic_url' => $this->string($item['profilePicUrl'] ?? ''),
            ],
            'analysis' => [
                'mvp_score' => $this->computeMvpScore($item),
                'next_action' => 'Segmenter le prospect puis proposer un angle de contenu Instagram.',
                'summary' => 'Profil Instagram prêt pour scoring et analyse IA.',
            ],
            'activity_log' => [
                'source' => 'apify/instagram_profile',
                'event' => 'enrichment_collected',
                'details' => 'Enrichissement social Instagram récupéré et normalisé.',
            ],
        ];
    }

    /** @param array<string, mixed> $item */
    private function mapInstagramHashtag(array $item): array
    {
        return [
            'prospect' => [
                'full_name' => $this->string($item['ownerFullName'] ?? ''),
                'business_name' => '',
                'activity' => 'instagram_hashtag',
                'city' => '',
                'country' => '',
                'website' => '',
            ],
            'social_profile' => [
                'platform' => 'instagram',
                'profile_url' => $this->string($item['ownerProfilePicUrl'] ?? ''),
                'username' => $this->string($item['ownerUsername'] ?? ''),
                'followers_count' => null,
                'engagement_rate' => null,
            ],
            'enrichment_data' => [
                'hashtag' => $this->string($item['queryTag'] ?? ''),
                'post_url' => $this->string($item['url'] ?? ''),
                'likes_count' => $item['likesCount'] ?? null,
                'comments_count' => $item['commentsCount'] ?? null,
            ],
            'analysis' => [
                'mvp_score' => $this->computeMvpScore($item),
                'next_action' => 'Utiliser le hashtag pour identifier d’autres prospects pertinents.',
                'summary' => 'Signal hashtag exploitable pour enrichissement progressif.',
            ],
            'activity_log' => [
                'source' => 'apify/instagram_hashtag',
                'event' => 'signal_collected',
                'details' => 'Données hashtag normalisées pour usage CRM.',
            ],
        ];
    }

    /** @param array<string, mixed> $item */
    private function mapLinkedInProfile(array $item): array
    {
        $fullName = trim($this->string($item['firstName'] ?? '') . ' ' . $this->string($item['lastName'] ?? ''));

        return [
            'prospect' => [
                'full_name' => trim($fullName) !== '' ? trim($fullName) : $this->string($item['fullName'] ?? ''),
                'first_name' => $this->string($item['firstName'] ?? ''),
                'last_name' => $this->string($item['lastName'] ?? ''),
                'business_name' => $this->string($item['companyName'] ?? ''),
                'activity' => $this->string($item['headline'] ?? ''),
                'city' => $this->string($item['geo']['city'] ?? ''),
                'country' => $this->string($item['geo']['country'] ?? ''),
                'website' => $this->string($item['companyWebsite'] ?? ''),
                'linkedin_url' => $this->string($item['profileUrl'] ?? ''),
            ],
            'social_profile' => [
                'platform' => 'linkedin',
                'profile_url' => $this->string($item['profileUrl'] ?? ''),
                'username' => $this->string($item['publicIdentifier'] ?? ''),
                'followers_count' => $item['followers'] ?? null,
                'engagement_rate' => null,
            ],
            'enrichment_data' => [
                'headline' => $this->string($item['headline'] ?? ''),
                'about' => $this->string($item['summary'] ?? ''),
                'experiences' => $item['experiences'] ?? [],
                'skills' => $item['skills'] ?? [],
            ],
            'analysis' => [
                'mvp_score' => $this->computeMvpScore($item),
                'next_action' => 'Prioriser une approche B2B personnalisée sur LinkedIn.',
                'summary' => 'Profil LinkedIn structuré pour conversion pipeline.',
            ],
            'activity_log' => [
                'source' => 'apify/linkedin_profile',
                'event' => 'enrichment_collected',
                'details' => 'Profil LinkedIn mappé vers les objets métier.',
            ],
        ];
    }

    /** @param array<string, mixed> $item */
    private function mapTikTok(array $item): array
    {
        $nickname = $this->string($item['authorMeta']['name'] ?? $item['authorMeta']['nickName'] ?? '');

        return [
            'prospect' => [
                'full_name' => $nickname,
                'business_name' => '',
                'activity' => 'creator',
                'city' => '',
                'country' => '',
                'website' => $this->string($item['authorMeta']['link'] ?? ''),
                'tiktok_url' => $this->string($item['authorMeta']['profileUrl'] ?? ''),
            ],
            'social_profile' => [
                'platform' => 'tiktok',
                'profile_url' => $this->string($item['authorMeta']['profileUrl'] ?? ''),
                'username' => $this->string($item['authorMeta']['name'] ?? ''),
                'followers_count' => $item['authorMeta']['fans'] ?? null,
                'engagement_rate' => null,
            ],
            'enrichment_data' => [
                'video_url' => $this->string($item['webVideoUrl'] ?? ''),
                'description' => $this->string($item['text'] ?? ''),
                'likes' => $item['diggCount'] ?? null,
                'comments' => $item['commentCount'] ?? null,
                'shares' => $item['shareCount'] ?? null,
            ],
            'analysis' => [
                'mvp_score' => $this->computeMvpScore($item),
                'next_action' => 'Lancer une séquence de contenu adaptée au ton TikTok.',
                'summary' => 'Signal TikTok consolidé pour enrichissement progressif.',
            ],
            'activity_log' => [
                'source' => 'apify/tiktok',
                'event' => 'enrichment_collected',
                'details' => 'Données TikTok normalisées pour le CRM IA.',
            ],
        ];
    }

    /** @param array<string, mixed> $item */
    private function mapGeneric(string $source, array $item): array
    {
        return [
            'prospect' => [
                'full_name' => $this->string($item['name'] ?? ''),
                'business_name' => $this->string($item['company'] ?? ''),
                'activity' => $source,
                'city' => '',
                'country' => '',
                'website' => $this->string($item['url'] ?? ''),
            ],
            'social_profile' => [
                'platform' => $source,
                'profile_url' => $this->string($item['url'] ?? ''),
            ],
            'enrichment_data' => [
                'raw' => $item,
            ],
            'analysis' => [
                'mvp_score' => $this->computeMvpScore($item),
                'next_action' => 'Compléter les informations avant import prospect.',
                'summary' => 'Mapping générique appliqué.',
            ],
            'activity_log' => [
                'source' => 'apify/' . $source,
                'event' => 'collected',
                'details' => 'Mapping générique utilisé.',
            ],
        ];
    }

    /** @param array<string, mixed> $item */
    private function computeMvpScore(array $item): int
    {
        $score = 0;
        foreach (['email', 'professional_email', 'phone', 'website', 'url', 'profileUrl'] as $field) {
            if (!empty($item[$field])) {
                $score += 12;
            }
        }

        if (!empty($item['followersCount']) || !empty($item['followers']) || !empty($item['authorMeta']['fans'])) {
            $score += 20;
        }

        if (!empty($item['reviewsCount']) || !empty($item['totalScore'])) {
            $score += 20;
        }

        return min(100, $score);
    }

    private function string(mixed $value): string
    {
        return trim((string) $value);
    }

    private function firstName(string $fullName): string
    {
        $parts = preg_split('/\s+/', trim($fullName));
        return $parts[0] ?? '';
    }

    private function lastName(string $fullName): string
    {
        $parts = preg_split('/\s+/', trim($fullName));
        if (!is_array($parts) || count($parts) <= 1) {
            return '';
        }

        array_shift($parts);
        return implode(' ', $parts);
    }

    private function extractEmailFromText(string $text): string
    {
        if ($text === '') {
            return '';
        }

        if (preg_match('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', $text, $matches) === 1) {
            return (string) ($matches[0] ?? '');
        }

        return '';
    }
}
