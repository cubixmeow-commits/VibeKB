package cli

import "testing"

func TestPHPMeetsFloor(t *testing.T) {
	cases := []struct {
		version string
		want    bool
	}{
		{"8.2.0", true},
		{"8.2.17", true},
		{"8.3.1", true},
		{"8.4.19", true},
		{"9.0.0", true},
		{"8.1.29", false},
		{"8.0.30", false},
		{"7.4.33", false},
		{"", false},
		{"8", false},
		{"8.2", true},
		{"garbage", false},
	}
	for _, c := range cases {
		if got := phpMeetsFloor(c.version); got != c.want {
			t.Errorf("phpMeetsFloor(%q) = %v, want %v", c.version, got, c.want)
		}
	}
}

func TestDigits(t *testing.T) {
	cases := map[string]string{
		"8":     "8",
		"8rc1":  "8",
		"17dev": "17",
		"":      "",
		"x9":    "",
	}
	for in, want := range cases {
		if got := digits(in); got != want {
			t.Errorf("digits(%q) = %q, want %q", in, got, want)
		}
	}
}
