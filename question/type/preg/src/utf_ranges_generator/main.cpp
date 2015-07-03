#include <stdlib.h>
#include <pcre.h>
#include <string>
#include <unicode/ustream.h>
#include <iostream>
#include <sstream>
#include <list>
#include <algorithm>

#define UTF8_MAX 0x10FFFF

bool preg_match(const char * regex, const char * string, int length)
{
    int options = PCRE_UTF8 | PCRE_DOTALL;
    const char * error;
    int erroffset;
    pcre * re = pcre_compile(regex, options, &error, &erroffset, NULL);
    int ovector[8];
    int count = pcre_exec(re, NULL, string, length, 0, 0, ovector, 8);
    pcre_free(re);
    return count > 0;
}

int main()
{
    const char * prefix = "public static function ";
    const char * suffix = "_ranges() {";
    const char * tab1 = "    ";
    const char * tab2 = "                     ";

    std::list<std::pair<std::string, std::string> > props;

    props.push_back(std::pair<std::string, std::string>("dot", "."));

    props.push_back(std::pair<std::string, std::string>("slashd", "\\d"));
    props.push_back(std::pair<std::string, std::string>("slashh", "\\h"));
    props.push_back(std::pair<std::string, std::string>("slashs", "\\s"));
    props.push_back(std::pair<std::string, std::string>("slashv", "\\v"));
    props.push_back(std::pair<std::string, std::string>("slashw", "\\w"));

    props.push_back(std::pair<std::string, std::string>("Llut", "\\p{L&}"));

    const std::string posix_names[] = {
        "alnum",
        "alpha",
        "ascii",
        "blank",
        "cntrl",
        "digit",
        "graph",
        "lower",
        "print",
        "punct",
        "space",
        "upper",
        "word",
        "xdigit"
    };

    int posix_cnt = sizeof(posix_names) / sizeof(posix_names[0]);
    for (int i = 0; i < posix_cnt; i++) {
        props.push_back(std::pair<std::string, std::string>(posix_names[i], "[[:" + posix_names[i] + ":]]"));
    }

    const std::string uprop_names[] = {
        "C",
        "Cc",
        "Cf",
        "Cn",
        "Co",
        "Cs",

        "L",
        "Ll",
        "Lm",
        "Lo",
        "Lt",
        "Lu",
        //"L&",

        "M",
        "Mc",
        "Me",
        "Mn",

        "N",
        "Nd",
        "Nl",
        "No",

        "P",
        "Pc",
        "Pd",
        "Pe",
        "Pf",
        "Pi",
        "Po",
        "Ps",

        "S",
        "Sc",
        "Sk",
        "Sm",
        "So",

        "Z",
        "Zl",
        "Zp",
        "Zs",

        "Xan",
        "Xps",
        "Xsp",
        "Xuc",
        "Xwd",

        "Arabic",
        "Armenian",
        "Avestan",
        "Balinese",
        "Bamum",
        "Batak",
        "Bengali",
        "Bopomofo",
        "Brahmi",
        "Braille",
        "Buginese",
        "Buhid",
        "Canadian_Aboriginal",
        "Carian",
        "Chakma",
        "Cham",
        "Cherokee",
        "Common",
        "Coptic",
        "Cuneiform",
        "Cypriot",
        "Cyrillic",
        "Deseret",
        "Devanagari",
        "Egyptian_Hieroglyphs",
        "Ethiopic",
        "Georgian",
        "Glagolitic",
        "Gothic",
        "Greek",
        "Gujarati",
        "Gurmukhi",
        "Han",
        "Hangul",
        "Hanunoo",
        "Hebrew",
        "Hiragana",
        "Imperial_Aramaic",
        "Inherited",
        "Inscriptional_Pahlavi",
        "Inscriptional_Parthian",
        "Javanese",
        "Kaithi",
        "Kannada",
        "Katakana",
        "Kayah_Li",
        "Kharoshthi",
        "Khmer",
        "Lao",
        "Latin",
        "Lepcha",
        "Limbu",
        "Linear_B",
        "Lisu",
        "Lycian",
        "Lydian",
        "Malayalam",
        "Mandaic",
        "Meetei_Mayek",
        "Meroitic_Cursive",
        "Meroitic_Hieroglyphs",
        "Miao",
        "Mongolian",
        "Myanmar",
        "New_Tai_Lue",
        "Nko",
        "Ogham",
        "Old_Italic",
        "Old_Persian",
        "Old_South_Arabian",
        "Old_Turkic",
        "Ol_Chiki",
        "Oriya",
        "Osmanya",
        "Phags_Pa",
        "Phoenician",
        "Rejang",
        "Runic",
        "Samaritan",
        "Saurashtra",
        "Sharada",
        "Shavian",
        "Sinhala",
        "Sora_Sompeng",
        "Sundanese",
        "Syloti_Nagri",
        "Syriac",
        "Tagalog",
        "Tagbanwa",
        "Tai_Le",
        "Tai_Tham",
        "Tai_Viet",
        "Takri",
        "Tamil",
        "Telugu",
        "Thaana",
        "Thai",
        "Tibetan",
        "Tifinagh",
        "Ugaritic",
        "Vai",
        "Yi"
    };

    int uprop_cnt = sizeof(uprop_names) / sizeof(uprop_names[0]);
    for (int i = 0; i < uprop_cnt; i++) {
        props.push_back(std::pair<std::string, std::string>(uprop_names[i], "\\p{" + uprop_names[i] + "}"));
    }

    for (std::list<std::pair<std::string, std::string> >::iterator it = props.begin(); it != props.end(); ++it) {
        std::string funcname = it->first;
        std::string regex = "^" + it->second + "$";

        std::list<std::string> allhex;

        for (int i = 0; i <= UTF8_MAX; i++) {
            UnicodeString ustr(i);
            std::string utf8;
            ustr.toUTF8String(utf8);

            bool res = preg_match(regex.c_str(), utf8.c_str(), utf8.length());
            if (res) {
                std::stringstream ss;
                ss << std::hex << i;

                std::string str = ss.str();
                std::transform(str.begin(), str.end(), str.begin(), ::toupper);

                if (str.length() == 1)
                    str = "000" + str;
                else if (str.length() == 2)
                    str = "00" + str;
                else if (str.length() == 3)
                    str = "0" + str;
                allhex.push_back(str);
            }
        }

        int prevdec = -1;
        std::string prevhex;
        std::cout << tab1 << prefix << funcname << suffix << std::endl;
        std::cout << tab1 << tab1 << "return array(";

        if (allhex.empty()) {
            std::cout << ");" << std::endl;
        } else {
            for (std::list<std::string>::iterator iterline = allhex.begin(); iterline != allhex.end(); ++iterline) {
                std::string line = *iterline;
                int newnum;
                std::stringstream ss;
                ss << std::hex << line;
                ss >> newnum;

                if (prevdec == -1) {
                    std::cout << "array(0=>0x" << line << ", ";
                } else if (newnum != prevdec + 1) {
                    std::cout << "1=>0x" << prevhex << ")," << std::endl;
                    std::cout << tab2 << "array(0=>0x" << line << ", ";
                }
                prevdec = newnum;
                prevhex = line;
            }
            std::cout << "1=>0x" << prevhex << "));" << std::endl;
        }
        std::cout << tab1 << "}" << std::endl << std::endl;
        //std::cout << "Done with " << funcname << std::endl;
    }

    //std::cout << "Done!\n";

    return 0;
}
