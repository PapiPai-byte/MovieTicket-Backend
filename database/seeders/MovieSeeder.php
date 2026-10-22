<?php

namespace Database\Seeders;

use App\Enums\MovieStatus;
use App\Models\Movie;
use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $movies = [
            [
                'title' => 'Dune: Part Two',
                'slug' => 'dune-part-two',
                'synopsis' => 'Paul Atreides unites with Chani and the Fremen while seeking revenge against the conspirators who destroyed his family.',
                'genre' => ['Sci-Fi', 'Adventure'],
                'duration_minutes' => 166,
                'rating' => 8.6,
                'release_date' => '2025-03-01',
                'status' => MovieStatus::NOW_SHOWING,
                'poster_url' => 'https://image.tmdb.org/t/p/w780/1pdfLvkbY9ohJlCjQH2CZjjYVvJ.jpg',
                'trailer_url' => 'https://www.youtube.com/embed/Way9Dexny3w',
            ],
            [
                'title' => 'Spider-Man: Across the Spider-Verse',
                'slug' => 'spider-man-across-the-spider-verse',
                'synopsis' => 'Miles Morales catapults across the Multiverse, encountering a team of Spider-People charged with protecting its very existence.',
                'genre' => ['Animation', 'Action', 'Adventure'],
                'duration_minutes' => 140,
                'rating' => 8.7,
                'release_date' => '2025-03-05',
                'status' => MovieStatus::NOW_SHOWING,
                'poster_url' => 'https://image.tmdb.org/t/p/w780/4HodYYKEIsGOdinkGi2Ucz6X9i0.jpg',
                'trailer_url' => 'https://www.youtube.com/embed/cqGjhVJWtEg',
            ],
            [
                'title' => 'The Batman',
                'slug' => 'the-batman',
                'synopsis' => 'When a sadistic serial killer begins murdering key political figures in Gotham, Batman is forced to investigate the city\'s hidden corruption.',
                'genre' => ['Action', 'Crime', 'Drama'],
                'duration_minutes' => 176,
                'rating' => 7.8,
                'release_date' => '2025-03-10',
                'status' => MovieStatus::NOW_SHOWING,
                'poster_url' => 'https://image.tmdb.org/t/p/w780/74xTEgt7R36Fpooo50r9T25onhq.jpg',
                'trailer_url' => 'https://www.youtube.com/embed/mqqft2x_Aa4',
            ],
            [
                'title' => 'Guardians of the Galaxy Vol. 3',
                'slug' => 'guardians-of-the-galaxy-vol-3',
                'synopsis' => 'Still reeling from the loss of Gamora, Peter Quill rallies his team to defend the universe and one of their own.',
                'genre' => ['Action', 'Sci-Fi', 'Comedy'],
                'duration_minutes' => 150,
                'rating' => 8.0,
                'release_date' => '2025-03-16',
                'status' => MovieStatus::NOW_SHOWING,
                'poster_url' => 'https://image.tmdb.org/t/p/w780/r2J02Z2OpNTctfOSN1Ydgii51I3.jpg',
                'trailer_url' => 'https://www.youtube.com/embed/u3V5KDHRQvk',
            ],
            [
                'title' => 'Barbie',
                'slug' => 'barbie',
                'synopsis' => 'Barbie and Ken are having the time of their lives in the colorful and seemingly perfect world of Barbie Land.',
                'genre' => ['Comedy', 'Adventure', 'Fantasy'],
                'duration_minutes' => 114,
                'rating' => 7.2,
                'release_date' => '2025-04-01',
                'status' => MovieStatus::COMING_SOON,
                'poster_url' => 'https://image.tmdb.org/t/p/w780/ctMserH8g2SeOAnCw5gFjdQF8mo.jpg',
                'trailer_url' => 'https://www.youtube.com/embed/pBk4NYhWNMM',
            ],
            [
                'title' => 'Oppenheimer',
                'slug' => 'oppenheimer',
                'synopsis' => 'The story of American scientist J. Robert Oppenheimer and his role in the development of the atomic bomb.',
                'genre' => ['Biography', 'Drama', 'History'],
                'duration_minutes' => 180,
                'rating' => 8.9,
                'release_date' => '2025-03-20',
                'status' => MovieStatus::NOW_SHOWING,
                'poster_url' => 'https://image.tmdb.org/t/p/w780/8Gxv8gSFCU0XGDykEGv7zR1n2ua.jpg',
                'trailer_url' => 'https://www.youtube.com/embed/uYPbbksJxIg',
            ],
            [
                'title' => 'Interstellar',
                'slug' => 'interstellar',
                'synopsis' => 'When Earth becomes uninhabitable in the future, a farmer and ex-NASA pilot is tasked to pilot a spacecraft along with a team of researchers.',
                'genre' => ['Sci-Fi', 'Drama', 'Adventure'],
                'duration_minutes' => 169,
                'rating' => 8.7,
                'release_date' => '2025-03-22',
                'status' => MovieStatus::NOW_SHOWING,
                'poster_url' => 'https://image.tmdb.org/t/p/w780/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg',
                'trailer_url' => 'https://www.youtube.com/embed/zSWdZVtXT7E',
            ],
            [
                'title' => 'Inception',
                'slug' => 'inception',
                'synopsis' => 'A thief who steals corporate secrets through the use of dream-sharing technology is given the inverse task of planting an idea into the mind of a C.E.O.',
                'genre' => ['Action', 'Sci-Fi', 'Thriller'],
                'duration_minutes' => 148,
                'rating' => 8.8,
                'release_date' => '2025-03-25',
                'status' => MovieStatus::NOW_SHOWING,
                'poster_url' => 'https://image.tmdb.org/t/p/w780/oYuLEt3zVCKq57qu2F8dT7NIa6f.jpg',
                'trailer_url' => 'https://www.youtube.com/embed/YoHD9XEInc0',
            ],
            [
                'title' => 'Deadpool & Wolverine',
                'slug' => 'deadpool-and-wolverine',
                'synopsis' => 'Wolverine is recovering from his injuries when he crosses paths with the loudmouth Deadpool. They team up to defeat a common enemy.',
                'genre' => ['Action', 'Comedy', 'Sci-Fi'],
                'duration_minutes' => 127,
                'rating' => 7.9,
                'release_date' => '2025-05-15',
                'status' => MovieStatus::COMING_SOON,
                'poster_url' => 'https://image.tmdb.org/t/p/w780/8cdWjvZQUExUUTzyp4t6EDMubfO.jpg',
                'trailer_url' => 'https://www.youtube.com/embed/73_1biulkYk',
            ],
            [
                'title' => 'Gladiator II',
                'slug' => 'gladiator-ii',
                'synopsis' => 'Years after witnessing the death of Maximus at the hands of his uncle, Lucius must enter the Colosseum after the emperors conquer his home.',
                'genre' => ['Action', 'Adventure', 'Drama'],
                'duration_minutes' => 148,
                'rating' => 7.5,
                'release_date' => '2025-06-01',
                'status' => MovieStatus::COMING_SOON,
                'poster_url' => 'https://image.tmdb.org/t/p/w780/2cxhvwyEwRlysAmRH4iodkvo0z5.jpg',
                'trailer_url' => 'https://www.youtube.com/embed/4rgYUipGJNo',
            ],
        ];

        foreach ($movies as $movieData) {
            Movie::updateOrCreate(
                ['slug' => $movieData['slug']],
                $movieData
            );
        }
    }
}

